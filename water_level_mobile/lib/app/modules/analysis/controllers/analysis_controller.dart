import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:syncfusion_flutter_datepicker/datepicker.dart';
import '../../../core/theme/app_theme.dart';
import '../../home/controllers/home_controller.dart';
import '../../../data/providers/api_provider.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:url_launcher/url_launcher.dart';

class AnalysisController extends GetxController {
  final homeController = Get.find<HomeController>();

  var isLoading = false.obs;
  var historyData = <Map<String, dynamic>>[].obs;
  var averageLevel = 0.0.obs;
  var minLevel = 0.0.obs;
  var maxLevel = 0.0.obs;
  var totalSamples = 0.obs;
  var searchQuery = ''.obs;
  
  List<Map<String, dynamic>> get filteredHistoryData {
    if (searchQuery.value.isEmpty) return historyData;
    return historyData.where((item) {
      final time = DateFormat('HH:mm').format(item['time']);
      final date = DateFormat('dd MMMM yyyy', 'id_ID').format(item['time']);
      final status = item['status'].toString().toLowerCase();
      final level = item['level'].toString();
      final query = searchQuery.value.toLowerCase();
      
      return time.contains(query) || 
             date.toLowerCase().contains(query) || 
             status.contains(query) || 
             level.contains(query);
    }).toList();
  }
  
  // Tab/Period Selection
  var selectedPeriod = 'Harian'.obs;
  final List<String> periods = ['Harian', 'Mingguan', 'Bulanan', 'Tahunan', 'Custom'];
  
  var startDate = DateTime.now().subtract(const Duration(days: 1)).obs;
  var endDate = DateTime.now().obs;

  @override
  void onInit() {
    super.onInit();
    fetchHistory();
    
    // Listen to device changes in HomeController
    ever(homeController.selectedDeviceSlug, (_) {
      fetchHistory();
    });
  }

  Future<void> fetchHistory() async {
    if (homeController.selectedDeviceSlug.value.isEmpty) return;
    
    isLoading.value = true;
    try {
      final apiProvider = Get.find<ApiProvider>(); // Or use homeController._apiProvider if it's public
      
      String rangeParam = 'daily';
      if (selectedPeriod.value == 'Harian') rangeParam = 'daily';
      else if (selectedPeriod.value == 'Mingguan') rangeParam = 'weekly';
      else if (selectedPeriod.value == 'Bulanan') rangeParam = 'monthly';
      else if (selectedPeriod.value == 'Tahunan') rangeParam = 'yearly';
      else if (selectedPeriod.value == 'Custom') rangeParam = 'custom';

      final response = await apiProvider.fetchHistory(
        slug: homeController.selectedDeviceSlug.value,
        range: rangeParam,
        startDate: DateFormat('yyyy-MM-dd').format(startDate.value),
        endDate: DateFormat('yyyy-MM-dd').format(endDate.value),
      );

      if (response != null && response['status'] == 'success') {
        final List<dynamic> data = response['data'];
        
        double sum = 0;
        double min = 9999.0;
        double max = -9999.0;

        final List<Map<String, dynamic>> processedData = [];
        final int durationDays = endDate.value.difference(startDate.value).inDays;
        
        // Use 6-hour aggregation for Weekly OR Custom range between 3-30 days
        bool useSixHourAggregation = rangeParam == 'weekly' || 
                                     (rangeParam == 'custom' && durationDays >= 3 && durationDays <= 30);

        if (useSixHourAggregation) {
          // Group by 6-hour blocks
          Map<String, List<double>> groups = {};
          Map<String, DateTime> groupTimes = {};

          for (var item in data) {
            final time = DateTime.parse(item['t']);
            final level = (item['y'] as num).toDouble();
            
            sum += level;
            if (level < min) min = level;
            if (level > max) max = level;

            final block = (time.hour / 6).floor();
            final key = '${DateFormat('yyyy-MM-dd').format(time)}-$block';
            
            if (!groups.containsKey(key)) {
              groups[key] = [];
              groupTimes[key] = DateTime(time.year, time.month, time.day, block * 6);
            }
            groups[key]!.add(level);
          }

          groups.forEach((key, levels) {
            final avg = levels.reduce((a, b) => a + b) / levels.length;
            final minVal = levels.reduce((a, b) => a < b ? a : b);
            final maxVal = levels.reduce((a, b) => a > b ? a : b);
            
            processedData.add({
              'time': groupTimes[key],
              'level': avg,
              'min': minVal,
              'max': maxVal,
              'status': avg > 1.8 ? 'Siaga 1' : 'Aman',
            });
          });
          
          processedData.sort((a, b) => (a['time'] as DateTime).compareTo(b['time'] as DateTime));
        } else {
          // Default mapping for Daily, Monthly, Yearly, and very short/long Custom ranges
          for (var item in data) {
            final level = (item['y'] as num).toDouble();
            final minVal = (item['min'] as num?)?.toDouble() ?? level;
            final maxVal = (item['max'] as num?)?.toDouble() ?? level;
            
            sum += level;
            if (level < min) min = level;
            if (level > max) max = level;

            processedData.add({
              'time': DateTime.parse(item['t']),
              'level': level,
              'min': minVal,
              'max': maxVal,
              'status': level > 1.8 ? 'Siaga 1' : 'Aman',
            });
          }
        }

        historyData.value = processedData;

        if (data.isNotEmpty) {
          averageLevel.value = sum / data.length;
          minLevel.value = min;
          maxLevel.value = max;
        } else {
          averageLevel.value = 0;
          minLevel.value = 0;
          maxLevel.value = 0;
        }
        totalSamples.value = data.length;
      }
    } catch (e) {
      debugPrint('Error fetching history: $e');
    } finally {
      isLoading.value = false;
      debugPrint('Fetch History Done. Total data: ${historyData.length}');
    }
  }

  void changePeriod(BuildContext context, String period) {
    selectedPeriod.value = period;
    if (period == 'Custom') {
      // Trigger the picker directly if Custom is selected from segments
      selectDateRange(context);
    } else {
      // Set appropriate start/end dates for the period
      final now = DateTime.now();
      if (period == 'Harian') {
        startDate.value = now.subtract(const Duration(days: 1));
      } else if (period == 'Mingguan') {
        startDate.value = now.subtract(const Duration(days: 7));
      } else if (period == 'Bulanan') {
        startDate.value = now.subtract(const Duration(days: 30));
      } else if (period == 'Tahunan') {
        startDate.value = now.subtract(const Duration(days: 365));
      }
      endDate.value = now;
      fetchHistory();
    }
  }

  Future<void> selectDateRange(BuildContext context) async {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    DateTime? tempStart = startDate.value;
    DateTime? tempEnd = endDate.value;

    showDialog(
      context: context,
      builder: (BuildContext context) {
        return Dialog(
          shape:
              RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
          backgroundColor: isDark ? const Color(0xFF1E293B) : Colors.white,
          elevation: 24,
          child: Container(
            padding: const EdgeInsets.all(20),
            height: 480,
            width: double.infinity,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Rentang Waktu',
                  style: GoogleFonts.plusJakartaSans(
                    fontWeight: FontWeight.w800,
                    fontSize: 18,
                    color: isDark ? Colors.white : const Color(0xFF0F172A),
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  'Pilih periode analisis data',
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 13,
                    color: context.textSecondary,
                  ),
                ),
                const SizedBox(height: 24),
                Expanded(
                  child: SfDateRangePicker(
                    onSelectionChanged:
                        (DateRangePickerSelectionChangedArgs args) {
                      if (args.value is PickerDateRange) {
                        tempStart = args.value.startDate;
                        tempEnd = args.value.endDate;
                      }
                    },
                    selectionMode: DateRangePickerSelectionMode.range,
                    initialSelectedRange:
                        PickerDateRange(startDate.value, endDate.value),
                    maxDate: DateTime.now(),
                    headerStyle: DateRangePickerHeaderStyle(
                      textAlign: TextAlign.center,
                      textStyle: GoogleFonts.plusJakartaSans(
                        fontWeight: FontWeight.w800,
                        fontSize: 16,
                        color: isDark ? Colors.white : const Color(0xFF0F172A),
                      ),
                    ),
                    monthCellStyle: DateRangePickerMonthCellStyle(
                      textStyle: GoogleFonts.plusJakartaSans(
                        fontWeight: FontWeight.w500,
                        color: isDark ? Colors.white : const Color(0xFF0F172A),
                      ),
                      todayTextStyle: GoogleFonts.plusJakartaSans(
                        fontWeight: FontWeight.bold,
                        color: AppColors.accent,
                      ),
                      leadingDatesTextStyle:
                          GoogleFonts.plusJakartaSans(color: context.textMuted),
                      trailingDatesTextStyle:
                          GoogleFonts.plusJakartaSans(color: context.textMuted),
                    ),
                    yearCellStyle: DateRangePickerYearCellStyle(
                      textStyle: GoogleFonts.plusJakartaSans(
                          color:
                              isDark ? Colors.white : const Color(0xFF0F172A)),
                      todayTextStyle: GoogleFonts.plusJakartaSans(
                          color: AppColors.accent, fontWeight: FontWeight.bold),
                    ),
                    monthViewSettings: const DateRangePickerMonthViewSettings(
                      firstDayOfWeek: 1,
                      enableSwipeSelection: true,
                    ),
                    selectionColor: AppColors.accent,
                    startRangeSelectionColor: AppColors.accent,
                    endRangeSelectionColor: AppColors.accent,
                    rangeSelectionColor: AppColors.accent.withOpacity(0.15),
                    todayHighlightColor: AppColors.accent,
                    showNavigationArrow: true,
                    selectionTextStyle: const TextStyle(
                        color: Colors.white, fontWeight: FontWeight.bold),
                    rangeTextStyle: GoogleFonts.plusJakartaSans(
                        color: isDark ? Colors.white : const Color(0xFF0F172A)),
                  ),
                ),
                const SizedBox(height: 16),
                Row(
                  children: [
                    Expanded(
                      child: TextButton(
                        onPressed: () => Navigator.pop(context),
                        style: TextButton.styleFrom(
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12)),
                        ),
                        child: Text(
                          'BATAL',
                          style: GoogleFonts.plusJakartaSans(
                            color: context.textSecondary,
                            fontWeight: FontWeight.w700,
                            letterSpacing: 1,
                            fontSize: 12,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: ElevatedButton(
                        onPressed: () {
                          if (tempStart != null) {
                            startDate.value = tempStart!;
                            // If user only selected one date, treat it as a single-day range
                            endDate.value = tempEnd ?? tempStart!;
                            selectedPeriod.value = 'Custom';
                            fetchHistory();
                            Navigator.pop(context);
                          } else {
                            Get.snackbar(
                              'Peringatan',
                              'Silakan pilih minimal satu tanggal',
                              snackPosition: SnackPosition.BOTTOM,
                              backgroundColor: Colors.orange,
                              colorText: Colors.white,
                            );
                          }
                        },
                        style: ElevatedButton.styleFrom(
                          backgroundColor: AppColors.accent,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          elevation: 0,
                          shadowColor: Colors.transparent,
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12)),
                        ),
                        child: Text(
                          'TERAPKAN',
                          style: GoogleFonts.plusJakartaSans(
                            fontWeight: FontWeight.w800,
                            letterSpacing: 1,
                            fontSize: 12,
                          ),
                        ),
                      ),
                    ),
                  ],
                )
              ],
            ),
          ),
        );
      },
    );
  }

  String get periodLabel {
    if (selectedPeriod.value == 'Custom') {
      return '${DateFormat('dd MMM').format(startDate.value)} - ${DateFormat('dd MMM').format(endDate.value)}';
    }
    return selectedPeriod.value;
  }

  Future<void> exportHistory() async {
    try {
      // Use the correct key from .env and handle the trailing /api correctly
      String baseUrl = dotenv.env['API_BASE_URL'] ?? 'http://103.172.205.35/api';
      
      // Remove trailing slash if exists
      if (baseUrl.endsWith('/')) baseUrl = baseUrl.substring(0, baseUrl.length - 1);
      
      final slug = homeController.selectedDeviceSlug.value;
      
      String rangeParam = 'daily';
      if (selectedPeriod.value == 'Harian') rangeParam = 'daily';
      else if (selectedPeriod.value == 'Mingguan') rangeParam = 'weekly';
      else if (selectedPeriod.value == 'Bulanan') rangeParam = 'monthly';
      else if (selectedPeriod.value == 'Tahunan') rangeParam = 'yearly';
      else if (selectedPeriod.value == 'Custom') rangeParam = 'custom';

      final start = DateFormat('yyyy-MM-dd').format(startDate.value);
      final end = DateFormat('yyyy-MM-dd').format(endDate.value);
      
      // Construct URL (baseUrl already includes /api)
      final url = '$baseUrl/water-level/export?device_slug=$slug&range=$rangeParam&start_date=$start&end_date=$end';
      
      debugPrint('Exporting to: $url');
      
      final uri = Uri.parse(url);
      
      // Attempt to launch directly. canLaunchUrl can be unreliable on some Android versions.
      bool launched = await launchUrl(
        uri, 
        mode: LaunchMode.externalApplication,
      );

      if (!launched) {
        throw 'Sistem tidak dapat menemukan aplikasi untuk membuka link unduhan. Pastikan browser terinstal.';
      }
    } catch (e) {
      debugPrint('Error exportHistory: $e');
      Get.snackbar(
        'Gagal Mengunduh',
        'Error: $e\n\nLink: ${dotenv.env['API_BASE_URL'] ?? 'http://103.172.205.35/api'}',
        snackPosition: SnackPosition.BOTTOM,
        backgroundColor: Colors.red,
        colorText: Colors.white,
        duration: const Duration(seconds: 5),
      );
    }
  }
}
