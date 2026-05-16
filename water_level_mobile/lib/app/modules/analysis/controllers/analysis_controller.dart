import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:syncfusion_flutter_datepicker/datepicker.dart';
import '../../../core/theme/app_theme.dart';
import '../../home/controllers/home_controller.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../data/repositories/sensor_repository.dart';
import '../../../core/utils/app_snackbar.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';

class AnalysisController extends GetxController {
  final homeController = Get.find<HomeController>();
  final SensorRepository _sensorRepo = Get.find<SensorRepository>();

  var isLoading = false.obs;
  var historyData = <Map<String, dynamic>>[].obs;
  var averageLevel = 0.0.obs;
  var minLevel = 0.0.obs;
  var maxLevel = 0.0.obs;
  var totalSamples = 0.obs;
  var searchQuery = ''.obs;
  var showOnlyCritical = false.obs;
  
  // Comparison Mode
  var isComparisonMode = false.obs;
  var comparisonDeviceSlug = ''.obs;
  var comparisonHistoryData = <Map<String, dynamic>>[].obs;
  var comparisonDeviceName = ''.obs;
  
  List<Map<String, dynamic>> get filteredHistoryData {
    List<Map<String, dynamic>> baseData = historyData;
    if (showOnlyCritical.value) {
      baseData = historyData.where((item) => item['status'] != 'Aman').toList();
    }

    if (searchQuery.value.isEmpty) return baseData;
    return baseData.where((item) {
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
      String rangeParam = _getRangeParam();
      
      // Fetch Main Device
      final mainResponse = await _sensorRepo.getHistory(
        slug: homeController.selectedDeviceSlug.value,
        range: rangeParam,
        startDate: DateFormat('yyyy-MM-dd').format(startDate.value),
        endDate: DateFormat('yyyy-MM-dd').format(endDate.value),
      );

      if (mainResponse != null && mainResponse['status'] == 'success') {
        final processed = _processHistoryData(mainResponse['data'], rangeParam);
        historyData.value = processed['list'];
        averageLevel.value = processed['avg'];
        minLevel.value = processed['min'];
        maxLevel.value = processed['max'];
        totalSamples.value = processed['count'];
      }

      // Fetch Comparison Device if enabled
      if (isComparisonMode.value && comparisonDeviceSlug.value.isNotEmpty) {
        final compResponse = await _sensorRepo.getHistory(
          slug: comparisonDeviceSlug.value,
          range: rangeParam,
          startDate: DateFormat('yyyy-MM-dd').format(startDate.value),
          endDate: DateFormat('yyyy-MM-dd').format(endDate.value),
        );
        if (compResponse != null && compResponse['status'] == 'success') {
          final processed = _processHistoryData(compResponse['data'], rangeParam);
          comparisonHistoryData.value = processed['list'];
        }
      } else {
        comparisonHistoryData.clear();
      }
    } catch (e) {
      debugPrint('Error fetching history: $e');
    } finally {
      isLoading.value = false;
    }
  }

  String _getRangeParam() {
    if (selectedPeriod.value == 'Harian') return 'daily';
    if (selectedPeriod.value == 'Mingguan') return 'weekly';
    if (selectedPeriod.value == 'Bulanan') return 'monthly';
    if (selectedPeriod.value == 'Tahunan') return 'yearly';
    return 'custom';
  }

  Map<String, dynamic> _processHistoryData(List<dynamic> data, String rangeParam) {
    double sum = 0;
    double min = 9999.0;
    double max = -9999.0;
    final List<Map<String, dynamic>> processedData = [];
    final int durationDays = endDate.value.difference(startDate.value).inDays;
    
    bool useSixHourAggregation = rangeParam == 'weekly' || 
                                 (rangeParam == 'custom' && durationDays >= 3 && durationDays <= 30);

    if (useSixHourAggregation) {
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
        processedData.add({
          'time': groupTimes[key],
          'level': avg,
          'status': avg > 1.8 ? 'Siaga 1' : 'Aman',
        });
      });
      processedData.sort((a, b) => (a['time'] as DateTime).compareTo(b['time'] as DateTime));
    } else {
      for (var item in data) {
        final level = (item['y'] as num).toDouble();
        sum += level;
        if (level < min) min = level;
        if (level > max) max = level;
        processedData.add({
          'time': DateTime.parse(item['t']),
          'level': level,
          'status': level > 1.8 ? 'Siaga 1' : 'Aman',
        });
      }
    }
    return {
      'list': processedData,
      'avg': data.isEmpty ? 0.0 : sum / data.length,
      'min': data.isEmpty ? 0.0 : min,
      'max': data.isEmpty ? 0.0 : max,
      'count': data.length,
    };
  }

  void changePeriod(BuildContext context, String period) {
    if (selectedPeriod.value == period) return;
    
    selectedPeriod.value = period;
    if (period == 'Custom') {
      selectDateRange(context);
    } else {
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
      Future.delayed(const Duration(milliseconds: 150), () {
        fetchHistory();
      });
    }
  }

  Future<void> selectDateRange(BuildContext context) async {
    DateTime? tempStart = startDate.value;
    DateTime? tempEnd = endDate.value;

    Get.bottomSheet(
      Container(
        padding: const EdgeInsets.symmetric(vertical: 20),
        decoration: BoxDecoration(
          color: context.bgCard,
          borderRadius: const BorderRadius.vertical(top: Radius.circular(24)),
          border: Border(top: BorderSide(color: context.borderColor, width: 1)),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Center(
              child: Container(
                width: 40,
                height: 4,
                decoration: BoxDecoration(
                  color: context.dividerColor.withValues(alpha: 0.2),
                  borderRadius: BorderRadius.circular(10),
                ),
              ),
            ),
            const SizedBox(height: 24),
            
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 24),
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(10),
                    decoration: BoxDecoration(
                      color: AppColors.accent.withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Icon(Icons.date_range_rounded, color: AppColors.accent, size: 20),
                  ),
                  const SizedBox(width: 16),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'RENTANG WAKTU',
                        style: GoogleFonts.inter(
                          fontWeight: FontWeight.w900,
                          fontSize: 12,
                          color: AppColors.accent,
                          letterSpacing: 1.0,
                        ),
                      ),
                      Text(
                        'Pilih periode analisis data',
                        style: GoogleFonts.inter(
                          fontSize: 11,
                          fontWeight: FontWeight.w500,
                          color: context.textMuted,
                        ),
                      ),
                    ],
                  ),
                  const Spacer(),
                  IconButton(
                    onPressed: () => Get.back(),
                    icon: Icon(Icons.close_rounded, color: context.textMuted, size: 20),
                  ),
                ],
              ),
            ),
            
            const SizedBox(height: 12),
            
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: SfDateRangePicker(
                onSelectionChanged: (DateRangePickerSelectionChangedArgs args) {
                  if (args.value is PickerDateRange) {
                    tempStart = args.value.startDate;
                    tempEnd = args.value.endDate;
                  }
                },
                selectionMode: DateRangePickerSelectionMode.range,
                initialSelectedRange: PickerDateRange(startDate.value, endDate.value),
                maxDate: DateTime.now(),
                selectionColor: AppColors.accent,
                startRangeSelectionColor: AppColors.accent,
                endRangeSelectionColor: AppColors.accent,
                rangeSelectionColor: AppColors.accent.withValues(alpha: 0.1),
                todayHighlightColor: AppColors.accent,
                headerStyle: DateRangePickerHeaderStyle(
                  textStyle: GoogleFonts.inter(
                    fontWeight: FontWeight.w700,
                    fontSize: 14,
                    color: context.textPrimary,
                  ),
                ),
                monthCellStyle: DateRangePickerMonthCellStyle(
                  textStyle: GoogleFonts.inter(fontSize: 12, color: context.textPrimary),
                  todayTextStyle: GoogleFonts.inter(fontSize: 12, fontWeight: FontWeight.bold, color: AppColors.accent),
                ),
              ),
            ),
            const SizedBox(height: 16),
            Padding(
              padding: const EdgeInsets.fromLTRB(24, 0, 24, 12),
              child: Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () => Get.back(),
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                        side: BorderSide(color: context.dividerColor.withValues(alpha: 0.2)),
                      ),
                      child: Text(
                        'BATAL',
                        style: GoogleFonts.inter(
                          fontWeight: FontWeight.w700,
                          fontSize: 12,
                          color: context.textMuted,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: ElevatedButton(
                      onPressed: () {
                        if (tempStart != null) {
                          startDate.value = tempStart!;
                          endDate.value = tempEnd ?? tempStart!;
                          selectedPeriod.value = 'Custom';
                          fetchHistory();
                          Get.back();
                        }
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppColors.accent,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                        elevation: 0,
                      ),
                      child: Text(
                        'TERAPKAN',
                        style: GoogleFonts.inter(
                          fontWeight: FontWeight.w800,
                          fontSize: 12,
                          letterSpacing: 0.5,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
      isScrollControlled: true,
    );
  }

  String get periodLabel {
    if (selectedPeriod.value == 'Custom') {
      return '${DateFormat('dd MMM').format(startDate.value)} - ${DateFormat('dd MMM').format(endDate.value)}';
    }
    return selectedPeriod.value;
  }

  Future<void> exportHistory() async {
    if (homeController.isGuest.value) {
      homeController.showGuestRestrictionModal();
      return;
    }
    
    try {
      final slug = homeController.selectedDeviceSlug.value;
      String rangeParam = _getRangeParam();
      final start = DateFormat('yyyy-MM-dd').format(startDate.value);
      final end = DateFormat('yyyy-MM-dd').format(endDate.value);
      final baseUrl = dotenv.env['BASE_URL'] ?? 'http://103.172.205.35.nip.io';
      final url = '$baseUrl/api/water-level/export?device_slug=$slug&range=$rangeParam&start_date=$start&end_date=$end';
      
      final uri = Uri.parse(url);
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    } catch (e) {
      AppSnackbar.show(title: 'Gagal Mengunduh', message: 'Cek koneksi Anda.', isError: true);
    }
  }

  void toggleComparison() {
    if (homeController.isGuest.value) {
      homeController.showGuestRestrictionModal();
      return;
    }
    isComparisonMode.value = !isComparisonMode.value;
    if (!isComparisonMode.value) {
      comparisonDeviceSlug.value = '';
      comparisonHistoryData.clear();
    }
    fetchHistory();
  }

  void setComparisonDevice(String slug, String name) {
    comparisonDeviceSlug.value = slug;
    comparisonDeviceName.value = name;
    fetchHistory();
  }
}
