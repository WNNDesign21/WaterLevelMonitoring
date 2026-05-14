import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:syncfusion_flutter_datepicker/datepicker.dart';
import '../../../core/theme/app_theme.dart';
import '../../home/controllers/home_controller.dart';

class AnalysisController extends GetxController {
  final homeController = Get.find<HomeController>();

  var isLoading = false.obs;
  var historyData = <Map<String, dynamic>>[].obs;
  var averageLevel = 0.0.obs;
  var minLevel = 0.0.obs;
  var maxLevel = 0.0.obs;
  var totalSamples = 0.obs;
  
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
      // Mock delay for UI feeling
      await Future.delayed(const Duration(milliseconds: 800));
      _generateMockData();
      
    } catch (e) {
      debugPrint('Error fetching history: $e');
    } finally {
      isLoading.value = false;
    }
  }

  void changePeriod(String period) {
    selectedPeriod.value = period;
    if (period != 'Custom') {
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
    
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return Dialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
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
                    onSelectionChanged: (DateRangePickerSelectionChangedArgs args) {
                      if (args.value is PickerDateRange) {
                        if (args.value.startDate != null && args.value.endDate != null) {
                          startDate.value = args.value.startDate;
                          endDate.value = args.value.endDate;
                        }
                      }
                    },
                    selectionMode: DateRangePickerSelectionMode.range,
                    initialSelectedRange: PickerDateRange(startDate.value, endDate.value),
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
                      leadingDatesTextStyle: GoogleFonts.plusJakartaSans(color: context.textMuted),
                      trailingDatesTextStyle: GoogleFonts.plusJakartaSans(color: context.textMuted),
                    ),
                    yearCellStyle: DateRangePickerYearCellStyle(
                      textStyle: GoogleFonts.plusJakartaSans(color: isDark ? Colors.white : const Color(0xFF0F172A)),
                      todayTextStyle: GoogleFonts.plusJakartaSans(color: AppColors.accent, fontWeight: FontWeight.bold),
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
                    selectionTextStyle: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                    rangeTextStyle: GoogleFonts.plusJakartaSans(color: isDark ? Colors.white : const Color(0xFF0F172A)),
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
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
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
                          selectedPeriod.value = 'Custom';
                          fetchHistory();
                          Navigator.pop(context);
                        },
                        style: ElevatedButton.styleFrom(
                          backgroundColor: AppColors.accent,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          elevation: 0,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
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

  void _generateMockData() {
    final List<Map<String, dynamic>> mock = [];
    final now = DateTime.now();
    double sum = 0;
    double min = 10.0;
    double max = 0.0;
    
    // Create a more realistic "fluctuating" data set
    for (int i = 23; i >= 0; i--) {
      final time = now.subtract(Duration(hours: i));
      final level = 1.3 + (0.3 * (i % 7 / 7.0)) + (0.2 * (i % 3 / 3.0));
      
      sum += level;
      if (level < min) min = level;
      if (level > max) max = level;

      mock.add({
        'time': time,
        'level': level,
        'status': level > 1.8 ? 'Siaga 1' : 'Aman',
      });
    }
    
    averageLevel.value = sum / mock.length;
    minLevel.value = min;
    maxLevel.value = max;
    totalSamples.value = mock.length;
    historyData.value = mock;
  }
  
  String get periodLabel {
    if (selectedPeriod.value == 'Custom') {
      return '${DateFormat('dd MMM').format(startDate.value)} - ${DateFormat('dd MMM').format(endDate.value)}';
    }
    return selectedPeriod.value;
  }
}
