import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'dart:math' as math;
import 'package:google_fonts/google_fonts.dart';
import 'package:fl_chart/fl_chart.dart';
import 'package:intl/intl.dart';
import '../../../core/theme/app_theme.dart';
import '../../../routes/app_pages.dart';
import '../controllers/analysis_controller.dart';

class AnalysisView extends GetView<AnalysisController> {
  const AnalysisView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: context.bgPrimary,
      body: SafeArea(
        child: Column(
          children: [
            _buildPremiumHeader(context),
            Expanded(
              child: SingleChildScrollView(
                physics: const BouncingScrollPhysics(),
                padding:
                    const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildSegmentedControl(context),
                    _buildDateRangeDisplay(context),
                    const SizedBox(height: 10),
                    _buildModernChartCard(context),
                    const SizedBox(height: 24),
                    _buildEnhancedHistorySection(context),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
      bottomNavigationBar: _buildBottomNav(context),
    );
  }

  Widget _buildDateRangeDisplay(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Obx(() {
            final start = DateFormat('dd MMM yyyy').format(controller.startDate.value);
            final end = DateFormat('dd MMM yyyy').format(controller.endDate.value);
            final isSameDay = start == end;

            return Row(
              children: [
                Icon(
                  Icons.event_note_rounded,
                  size: 14,
                  color: context.textMuted.withValues(alpha: 0.6),
                ),
                const SizedBox(width: 8),
                Text(
                  isSameDay ? start : '$start - $end',
                  style: GoogleFonts.rajdhani(
                    fontSize: 13,
                    fontWeight: FontWeight.w700,
                    color: context.textMuted,
                    letterSpacing: 0.3,
                  ),
                ),
              ],
            );
          }),
          Obx(() {
            final isCustomActive = controller.selectedPeriod.value == 'Custom';
            return GestureDetector(
              onTap: () => controller.selectDateRange(context),
              behavior: HitTestBehavior.opaque,
              child: AnimatedContainer(
                duration: const Duration(milliseconds: 200),
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                decoration: BoxDecoration(
                  color: isCustomActive 
                      ? AppColors.accent 
                      : AppColors.accent.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(10),
                  boxShadow: isCustomActive ? [
                    BoxShadow(
                      color: AppColors.accent.withValues(alpha: 0.3),
                      blurRadius: 8,
                      offset: const Offset(0, 3),
                    )
                  ] : [],
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(
                      Icons.calendar_month_rounded,
                      size: 13,
                      color: isCustomActive ? Colors.white : AppColors.accent,
                    ),
                    const SizedBox(width: 6),
                    Text(
                      'CUSTOM',
                      style: GoogleFonts.inter(
                        fontSize: 9,
                        fontWeight: FontWeight.w900,
                        color: isCustomActive ? Colors.white : AppColors.accent,
                        letterSpacing: 0.5,
                      ),
                    ),
                  ],
                ),
              ),
            );
          }),
        ],
      ),
    );
  }

  // ── PREMIUM HEADER ────────────────────────────────────────────────
  Widget _buildPremiumHeader(BuildContext context) {
    return Container(
      padding: const EdgeInsets.fromLTRB(24, 20, 24, 16),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Expanded(
            child: GestureDetector(
              onTap: () => _showDevicePicker(context),
              behavior: HitTestBehavior.opaque,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'HISTORY ANALISIS',
                    style: GoogleFonts.inter(
                      fontSize: 11,
                      fontWeight: FontWeight.w800,
                      color: context.textPrimary,
                      letterSpacing: 0.5,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Row(
                    children: [
                      Obx(() => Flexible(
                            child: Text(
                              controller.homeController.selectedDeviceName.value,
                              style: GoogleFonts.inter(
                                fontSize: 16,
                                fontWeight: FontWeight.w900,
                                color: context.textPrimary,
                                letterSpacing: -0.5,
                              ),
                              overflow: TextOverflow.ellipsis,
                            ),
                          )),
                      const SizedBox(width: 6),
                      Icon(
                        Icons.keyboard_arrow_down_rounded,
                        color: context.textMuted.withValues(alpha: 0.6),
                        size: 20,
                      ),
                    ],
                  ),
                  const SizedBox(height: 2),
                  Obx(() => Row(
                        children: [
                          Icon(
                            Icons.location_on_rounded,
                            size: 10,
                            color: AppColors.accent.withValues(alpha: 0.7),
                          ),
                          const SizedBox(width: 4),
                          Text(
                            controller.homeController.selectedDeviceLocation.value.toUpperCase(),
                            style: GoogleFonts.inter(
                              fontSize: 10,
                              fontWeight: FontWeight.w800,
                              color: context.textMuted,
                              letterSpacing: 0.5,
                            ),
                          ),
                        ],
                      )),
                ],
              ),
            ),
          ),
          _headerAction(context, Icons.refresh_rounded, 
            onTap: () => controller.fetchHistory()),
        ],
      ),
    );
  }

  Widget _headerAction(BuildContext context, IconData icon, {VoidCallback? onTap}) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(10),
        decoration: BoxDecoration(
          color: context.bgCard,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: context.borderColor),
        ),
        child: Icon(icon, color: AppColors.accent, size: 20),
      ),
    );
  }

  // ── SEGMENTED CONTROL ─────────────────────────────────────────────
  Widget _buildSegmentedControl(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 10),
      child: LayoutBuilder(builder: (context, constraints) {
        final totalWidth = constraints.maxWidth;

        return Container(
          height: 44,
          padding: const EdgeInsets.all(4),
          decoration: BoxDecoration(
            color: context.isDark
                ? const Color(0xFF0F172A)
                : const Color(0xFFE2E8F0), // Slightly darker for better visibility
            borderRadius: BorderRadius.circular(100),
            border: Border.all(
              color: context.isDark 
                  ? Colors.white.withValues(alpha: 0.05)
                  : Colors.black.withValues(alpha: 0.05),
              width: 1,
            ),
          ),
          child: Obx(() {
            final visiblePeriods = ['Harian', 'Mingguan', 'Bulanan', 'Tahunan'];
            final selectedIndex =
                visiblePeriods.indexOf(controller.selectedPeriod.value);
            // Fallback if 'Custom' is selected elsewhere
            final safeIndex = selectedIndex == -1 ? 0 : selectedIndex;
            final itemWidth = (totalWidth - 8) / visiblePeriods.length;

            return Stack(
              children: [
                // Perfect Pill Indicator
                AnimatedPositioned(
                  duration: const Duration(milliseconds: 300),
                  curve: Curves.easeOutCubic,
                  left: safeIndex * itemWidth,
                  top: 0,
                  bottom: 0,
                  width: itemWidth,
                  child: Container(
                    decoration: BoxDecoration(
                      color: context.isDark ? const Color(0xFF1E293B) : Colors.white,
                      borderRadius: BorderRadius.circular(100),
                      border: Border.all(
                        color: AppColors.accent.withValues(alpha: 0.3),
                        width: 1.5,
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withValues(
                              alpha: context.isDark ? 0.3 : 0.04),
                          blurRadius: 6,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                  ),
                ),

                // Labels Row
                Row(
                  children: visiblePeriods.map((period) {
                    final isSelected =
                        controller.selectedPeriod.value == period;

                    return Expanded(
                      child: GestureDetector(
                        onTap: () => controller.changePeriod(context, period),
                        behavior: HitTestBehavior.opaque,
                        child: Center(
                          child: Text(
                            period.toUpperCase(),
                            style: GoogleFonts.inter(
                              fontSize: 10,
                              fontWeight: isSelected
                                  ? FontWeight.w900
                                  : FontWeight.w700,
                              color: isSelected
                                  ? AppColors.accent
                                  : const Color(0xFF64748B),
                              letterSpacing: 0.8,
                            ),
                          ),
                        ),
                      ),
                    );
                  }).toList(),
                ),
              ],
            );
          }),
        );
      }),
    );
  }

  // ── MODERN CHART CARD ─────────────────────────────────────────────
  Widget _buildModernChartCard(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: context.bgCard,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(
          color: context.dividerColor.withValues(alpha: 0.1),
          width: 1,
        ),
      ),
      child: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(24, 24, 24, 0),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'FLUKTUASI LEVEL AIR',
                      style: GoogleFonts.inter(
                        fontSize: 10,
                        fontWeight: FontWeight.w800,
                        color: context.textMuted,
                        letterSpacing: 1.2,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Obx(() => Text(
                          controller.periodLabel.toUpperCase(),
                          style: GoogleFonts.rajdhani(
                            fontSize: 18,
                            fontWeight: FontWeight.w800,
                            color: context.textPrimary,
                            letterSpacing: -0.2,
                          ),
                        )),
                  ],
                ),
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                  decoration: BoxDecoration(
                    color: AppColors.statusSafe.withOpacity(0.12),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Row(
                    children: [
                      const Icon(Icons.water_drop_rounded,
                          color: AppColors.statusSafe, size: 14),
                      const SizedBox(width: 6),
                      Text(
                        'Meter (m)',
                        style: GoogleFonts.inter(
                          fontSize: 11,
                          fontWeight: FontWeight.w800,
                          color: AppColors.statusSafe,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 40),
          SizedBox(
            height: 240,
            child: Obx(() {
              if (controller.isLoading.value) {
                return const Center(
                    child: CircularProgressIndicator(strokeWidth: 2.5));
              }
              return Padding(
                padding: const EdgeInsets.only(right: 24, left: 12),
                child: _buildRefinedChart(context),
              );
            }),
          ),
          const SizedBox(height: 20),
          _buildStatsRow(context),
        ],
      ),
    );
  }

  Widget _buildStatsRow(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 12),
      decoration: BoxDecoration(
        color: context.isDark
            ? Colors.white.withValues(alpha: 0.02)
            : Colors.black.withValues(alpha: 0.01),
        borderRadius: const BorderRadius.vertical(bottom: Radius.circular(24)),
      ),
      child: Column(
        children: [
          Row(
            children: [
              Expanded(
                child: Obx(() => _buildModernMiniStat(
                      context,
                      'TERENDAH',
                      controller.minLevel.value.toStringAsFixed(2),
                      Icons.arrow_downward_rounded,
                      const Color(0xFF3B82F6),
                    )),
              ),
              _statDivider(context),
              Expanded(
                child: Obx(() => _buildModernMiniStat(
                      context,
                      'TERTINGGI',
                      controller.maxLevel.value.toStringAsFixed(2),
                      Icons.arrow_upward_rounded,
                      const Color(0xFFEF4444),
                    )),
              ),
            ],
          ),
          Padding(
            padding: const EdgeInsets.symmetric(vertical: 16),
            child: Divider(
                color: context.dividerColor.withValues(alpha: 0.4), // Increased visibility
                height: 1,
                indent: 20,
                endIndent: 20),
          ),
          Row(
            children: [
              Expanded(
                child: Obx(() => _buildModernMiniStat(
                      context,
                      'RATA-RATA',
                      controller.averageLevel.value.toStringAsFixed(2),
                      Icons.analytics_rounded,
                      AppColors.accent,
                    )),
              ),
              _statDivider(context),
              Expanded(
                child: Obx(() => _buildModernMiniStat(
                      context,
                      'SAMPEL DATA',
                      '${controller.totalSamples.value}',
                      Icons.query_stats_rounded,
                      const Color(0xFF8B5CF6),
                      unit: ' data',
                    )),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _statDivider(BuildContext context) {
    return Container(
      width: 1,
      height: 40,
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            context.dividerColor.withValues(alpha: 0),
            context.dividerColor.withValues(alpha: 0.8), // Increased visibility
            context.dividerColor.withValues(alpha: 0),
          ],
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
        ),
      ),
    );
  }

  Widget _buildModernMiniStat(BuildContext context, String label, String value,
      IconData icon, Color color,
      {String unit = ' m'}) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(icon, color: color.withValues(alpha: 0.8), size: 11),
              const SizedBox(width: 6),
              Text(
                label,
                style: GoogleFonts.inter(
                  fontSize: 9,
                  fontWeight: FontWeight.w800,
                  color: context.textMuted.withValues(alpha: 0.8),
                  letterSpacing: 0.5,
                ),
              ),
            ],
          ),
          const SizedBox(height: 6),
          Row(
            crossAxisAlignment: CrossAxisAlignment.baseline,
            textBaseline: TextBaseline.alphabetic,
            children: [
              Text(
                value,
                style: GoogleFonts.rajdhani(
                  fontSize: 26,
                  fontWeight: FontWeight.w800,
                  color: context.textPrimary,
                  height: 1,
                ),
              ),
              const SizedBox(width: 4),
              Text(
                unit,
                style: GoogleFonts.inter(
                  fontSize: 11,
                  fontWeight: FontWeight.w700,
                  color: context.textSecondary.withValues(alpha: 0.6),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildRefinedChart(BuildContext context) {
    if (controller.historyData.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.query_stats_rounded,
                color: context.dividerColor, size: 40),
            const SizedBox(height: 12),
            Text(
              'Data tidak tersedia untuk periode ini',
              style: GoogleFonts.inter(
                color: context.textMuted,
                fontSize: 12,
                fontWeight: FontWeight.w600,
              ),
            ),
          ],
        ),
      );
    }

    final spots = controller.historyData.asMap().entries.map((e) {
      return FlSpot(e.key.toDouble(), (e.value['level'] as num).toDouble());
    }).toList();

    double minY = spots.map((s) => s.y).reduce((a, b) => a < b ? a : b) - 0.2;
    double maxY = spots.map((s) => s.y).reduce((a, b) => a > b ? a : b) + 0.2;

    // Safety for same values
    if (minY == maxY) {
      minY -= 0.5;
      maxY += 0.5;
    }

    final double yAxisWidth = 40.0;
    final double contentWidth =
        math.max(Get.width - 72, spots.length * 45.0);

    return Row(
      children: [
        // Sticky Y-Axis
        SizedBox(
          width: yAxisWidth,
          height: 250,
          child: LineChart(
            LineChartData(
              minY: minY,
              maxY: maxY,
              gridData: const FlGridData(show: false),
              borderData: FlBorderData(show: false),
              titlesData: FlTitlesData(
                leftTitles: AxisTitles(
                  sideTitles: SideTitles(
                    showTitles: true,
                    reservedSize: yAxisWidth,
                    interval: (maxY - minY) / 5 > 0 ? (maxY - minY) / 5 : 1,
                    getTitlesWidget: (value, meta) {
                      return Text(
                        value.toStringAsFixed(1),
                        style: GoogleFonts.inter(
                          fontSize: 9,
                          color: context.textMuted,
                          fontWeight: FontWeight.w700,
                        ),
                      );
                    },
                  ),
                ),
                rightTitles:
                    const AxisTitles(sideTitles: SideTitles(showTitles: false)),
                topTitles:
                    const AxisTitles(sideTitles: SideTitles(showTitles: false)),
                bottomTitles:
                    const AxisTitles(sideTitles: SideTitles(showTitles: false)),
              ),
              lineBarsData: [
                LineChartBarData(spots: [FlSpot(0, minY)], show: false)
              ],
            ),
          ),
        ),
        // Scrollable Chart
        Expanded(
          child: SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            physics: const BouncingScrollPhysics(),
            child: SizedBox(
              width: contentWidth,
              height: 250,
              child: LineChart(
                LineChartData(
                  lineTouchData: LineTouchData(
                    touchSpotThreshold: 20,
                    handleBuiltInTouches: true,
                    getTouchedSpotIndicator:
                        (LineChartBarData barData, List<int> spotIndexes) {
                      return spotIndexes.map((index) {
                        return TouchedSpotIndicatorData(
                          FlLine(
                            color: AppColors.accent.withOpacity(0.3),
                            strokeWidth: 2,
                            dashArray: [6, 4],
                          ),
                          FlDotData(
                            show: true,
                            getDotPainter: (spot, percent, barData, index) =>
                                FlDotCirclePainter(
                              radius: 5,
                              color: AppColors.accent,
                              strokeWidth: 2,
                              strokeColor: context.bgCard,
                            ),
                          ),
                        );
                      }).toList();
                    },
                    touchTooltipData: LineTouchTooltipData(
                      getTooltipColor: (spot) => const Color(0xFF1E293B),
                      tooltipBorderRadius: BorderRadius.circular(8),
                      tooltipPadding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                      tooltipMargin: 12,
                      fitInsideHorizontally: true,
                      fitInsideVertically: true,
                      getTooltipItems: (touchedSpots) {
                        return touchedSpots.map((spot) {
                          final data = controller.historyData[spot.spotIndex];
                          final time = data['time'] as DateTime;
                          final period = controller.selectedPeriod.value;
                          
                          String format = 'dd MMM yyyy';
                          if (period == 'Harian') {
                            format = 'HH:mm';
                          } else if (period == 'Mingguan') {
                            format = 'dd MMM, HH:mm';
                          }
                          
                          final timeStr = DateFormat(format).format(time);
                          return LineTooltipItem(
                            '$timeStr\n',
                            GoogleFonts.inter(
                              color: Colors.white.withOpacity(0.7),
                              fontSize: 9,
                              fontWeight: FontWeight.bold,
                            ),
                            children: [
                              TextSpan(
                                text: '${spot.y.toStringAsFixed(2)} m',
                                style: GoogleFonts.rajdhani(
                                  color: Colors.white,
                                  fontSize: 14,
                                  fontWeight: FontWeight.w800,
                                ),
                              ),
                            ],
                          );
                        }).toList();
                      },
                    ),
                  ),
                  gridData: FlGridData(
                    show: true,
                    drawVerticalLine: true,
                    horizontalInterval:
                        (maxY - minY) / 5 > 0 ? (maxY - minY) / 5 : 1,
                    getDrawingHorizontalLine: (value) => FlLine(
                      color: context.dividerColor.withOpacity(0.05),
                      strokeWidth: 1,
                    ),
                    getDrawingVerticalLine: (value) => FlLine(
                      color: context.dividerColor.withOpacity(0.05),
                      strokeWidth: 1,
                    ),
                  ),
                  titlesData: FlTitlesData(
                    leftTitles: const AxisTitles(
                        sideTitles: SideTitles(showTitles: false)),
                    rightTitles: const AxisTitles(
                        sideTitles: SideTitles(showTitles: false)),
                    topTitles: const AxisTitles(
                        sideTitles: SideTitles(showTitles: false)),
                    bottomTitles: AxisTitles(
                      sideTitles: SideTitles(
                        showTitles: true,
                        reservedSize: 48,
                        interval: 1,
                        getTitlesWidget: (value, meta) {
                          final index = value.toInt();
                          if (index < 0 ||
                              index >= controller.historyData.length) {
                            return const SizedBox();
                          }
                          final data = controller.historyData[index];
                          final time = data['time'] as DateTime;
                          final period = controller.selectedPeriod.value;

                          String label = '';
                          if (period == 'Harian') {
                            label = DateFormat('HH:mm').format(time);
                          } else if (period == 'Mingguan') {
                            label = DateFormat('dd/MM HH:mm').format(time);
                          } else if (period == 'Bulanan' ||
                              (period == 'Custom' &&
                                  controller.historyData.length < 100)) {
                            label = DateFormat('dd/MM').format(time);
                          } else if (period == 'Tahunan') {
                            label = DateFormat('dd/MM/yy').format(time);
                          } else {
                            // Default for long custom range
                            label = DateFormat('dd/MM').format(time);
                          }

                          return SideTitleWidget(
                            meta: meta,
                            space: 10,
                            angle: (period == 'Mingguan' || period == 'Bulanan' || period == 'Tahunan' || period == 'Custom') ? -0.8 : 0,
                            child: Text(
                              label,
                              style: GoogleFonts.inter(
                                fontSize: 9,
                                color: context.textMuted,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          );
                        },
                      ),
                    ),
                  ),
                  borderData: FlBorderData(show: false),
                  minY: minY,
                  maxY: maxY,
                  lineBarsData: [
                    LineChartBarData(
                      spots: spots,
                      isCurved: true,
                      barWidth: 4,
                      isStrokeCapRound: true,
                      gradient: const LinearGradient(
                        colors: [AppColors.accent, Color(0xFF6366F1)],
                      ),
                      dotData: FlDotData(
                        show: spots.length < 50 || controller.selectedPeriod.value == 'Tahunan',
                        getDotPainter: (spot, percent, barData, index) =>
                            FlDotCirclePainter(
                          radius: 2,
                          color: AppColors.accent,
                          strokeWidth: 1.5,
                          strokeColor: context.bgCard,
                        ),
                      ),
                      belowBarData: BarAreaData(
                        show: true,
                        gradient: LinearGradient(
                          colors: [
                            AppColors.accent.withOpacity(0.2),
                            AppColors.accent.withOpacity(0),
                          ],
                          begin: Alignment.topCenter,
                          end: Alignment.bottomCenter,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ),
      ],
    );

  }

  // ── ENHANCED HISTORY SECTION ──────────────────────────────────────
  Widget _buildEnhancedHistorySection(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 12),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'RIWAYAT LOG DATA',
                style: GoogleFonts.inter(
                  fontSize: 11,
                  fontWeight: FontWeight.w800,
                  color: context.textPrimary,
                  letterSpacing: 0.5,
                ),
              ),
              InkWell(
                onTap: () => Get.toNamed(Routes.HISTORY_LOG),
                borderRadius: BorderRadius.circular(8),
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                  child: Text(
                    'Lihat Semua',
                    style: GoogleFonts.inter(
                      fontSize: 11,
                      fontWeight: FontWeight.w700,
                      color: AppColors.accent,
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
        ConstrainedBox(
          constraints: const BoxConstraints(maxHeight: 450),
          child: Container(
            decoration: BoxDecoration(
              color: context.bgCard,
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: context.borderColor),
            ),
            clipBehavior: Clip.antiAlias,
            child: Obx(() {
              if (controller.historyData.isEmpty) {
                return Padding(
                  padding: const EdgeInsets.all(40),
                  child: Center(
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.history_rounded,
                            size: 48, color: context.dividerColor),
                        const SizedBox(height: 16),
                        Text(
                          'Belum ada data log',
                          style: GoogleFonts.inter(
                            color: context.textMuted,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              }
              return Theme(
                data: Theme.of(context).copyWith(
                  scrollbarTheme: ScrollbarThemeData(
                    thumbColor: WidgetStateProperty.all(AppColors.accent.withValues(alpha: 0.2)),
                    thickness: WidgetStateProperty.all(4),
                    radius: const Radius.circular(10),
                  ),
                ),
                child: Scrollbar(
                  child: ListView.separated(
                    padding: EdgeInsets.zero,
                    itemCount: controller.historyData.length,
                    separatorBuilder: (context, index) => Divider(
                        height: 1, color: context.dividerColor.withValues(alpha: 0.1)),
                    itemBuilder: (context, index) {
                      final data = controller.historyData[index];
                      return _buildModernHistoryItem(context, data);
                    },
                  ),
                ),
              );
            }),
          ),
        ),
      ],
    );
  }

  Widget _buildModernHistoryItem(
      BuildContext context, Map<String, dynamic> data) {
    final time = DateFormat('HH:mm').format(data['time']);
    final date = DateFormat('dd MMM yyyy').format(data['time']);
    final isAman = data['status'] == 'Aman';

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      child: Row(
        children: [
          // Time Visual
          Container(
            width: 54,
            padding: const EdgeInsets.symmetric(vertical: 8),
            decoration: BoxDecoration(
              color: context.isDark
                  ? Colors.white.withOpacity(0.05)
                  : Colors.black.withOpacity(0.03),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Column(
              children: [
                Text(
                  time,
                  style: GoogleFonts.inter(
                    fontSize: 13,
                    fontWeight: FontWeight.w800,
                    color: context.textPrimary,
                  ),
                ),
                Text(
                  'WIB',
                  style: GoogleFonts.inter(
                    fontSize: 8,
                    fontWeight: FontWeight.w700,
                    color: context.textMuted,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Text(
                      data['level'].toStringAsFixed(2),
                      style: GoogleFonts.rajdhani(
                        fontSize: 22,
                        fontWeight: FontWeight.w800,
                        color: context.textPrimary,
                      ),
                    ),
                    const SizedBox(width: 4),
                    Text(
                      'meter',
                      style: GoogleFonts.inter(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: context.textSecondary,
                      ),
                    ),
                  ],
                ),
                Text(
                  date,
                  style: GoogleFonts.inter(
                    fontSize: 10,
                    fontWeight: FontWeight.w600,
                    color: context.textMuted,
                  ),
                ),
              ],
            ),
          ),
          // Status Badge
          _statusBadge(isAman, data['status']),
        ],
      ),
    );
  }

  Widget _statusBadge(bool isAman, String status) {
    final color = isAman ? AppColors.statusSafe : AppColors.statusSiaga1;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: color.withOpacity(0.12),
        borderRadius: BorderRadius.circular(100),
        border: Border.all(color: color.withOpacity(0.2), width: 1),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 6,
            height: 6,
            decoration: BoxDecoration(
              color: color,
              shape: BoxShape.circle,
            ),
          ),
          const SizedBox(width: 8),
          Text(
            status.toUpperCase(),
            style: GoogleFonts.inter(
              fontSize: 10,
              fontWeight: FontWeight.w900,
              color: color,
            ),
          ),
        ],
      ),
    );
  }

  // ── BOTTOM NAV & MODALS ──────────────────────────────────────────
  Widget _buildBottomNav(BuildContext context) {
    return Container(
      height: 90,
      padding: const EdgeInsets.only(bottom: 20),
      decoration: BoxDecoration(
        color: context.bgCard,
        border: Border(top: BorderSide(color: context.borderColor, width: 0.5)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          _navItem(context, Icons.dashboard_rounded, 'Dashboard', false,
              onTap: () => Get.offAllNamed(Routes.HOME)),
          _navItem(context, Icons.analytics_rounded, 'Analisis', true),
          _navItem(context, Icons.map_rounded, 'Peta', false,
              onTap: () => Get.toNamed(Routes.DEVICE_MAP)),
          _navItem(context, Icons.settings_rounded, 'Pengaturan', false,
              onTap: () => Get.toNamed(Routes.SETTINGS)),
        ],
      ),
    );
  }

  Widget _navItem(
      BuildContext context, IconData icon, String label, bool active,
      {VoidCallback? onTap}) {
    return GestureDetector(
      onTap: onTap,
      behavior: HitTestBehavior.opaque,
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            icon,
            color: active ? AppColors.accent : context.textMuted,
            size: 24,
          ),
          const SizedBox(height: 6),
          Text(
            label,
            style: GoogleFonts.inter(
              fontSize: 10,
              fontWeight: active ? FontWeight.w800 : FontWeight.w600,
              color: active ? AppColors.accent : context.textMuted,
            ),
          ),
        ],
      ),
    );
  }

  void _showDevicePicker(BuildContext context) {
    final homeController = controller.homeController;
    final searchController = TextEditingController();
    final filteredDevices = <Map<String, dynamic>>[].obs;
    filteredDevices.value = homeController.devices;

    Get.bottomSheet(
      Container(
        height: MediaQuery.of(context).size.height * 0.75,
        padding: const EdgeInsets.fromLTRB(24, 12, 24, 0),
        decoration: BoxDecoration(
          color: context.bgCard,
          borderRadius: const BorderRadius.vertical(top: Radius.circular(30)),
        ),
        child: Column(
          children: [
            Container(
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: context.dividerColor.withValues(alpha: 0.5),
                borderRadius: BorderRadius.circular(2),
              ),
            ),
            const SizedBox(height: 24),
            Text(
              'Pilih Lokasi Pemantauan',
              style: GoogleFonts.inter(
                fontSize: 18,
                fontWeight: FontWeight.w800,
                color: context.textPrimary,
              ),
            ),
            const SizedBox(height: 24),
            TextField(
              controller: searchController,
              onChanged: (val) {
                filteredDevices.value = homeController.devices
                    .where((d) =>
                        (d['name'] ?? '')
                            .toLowerCase()
                            .contains(val.toLowerCase()) ||
                        (d['location'] ?? '')
                            .toLowerCase()
                            .contains(val.toLowerCase()))
                    .toList();
              },
              style: GoogleFonts.inter(
                  fontSize: 13, fontWeight: FontWeight.w500),
              decoration: InputDecoration(
                hintText: 'Cari lokasi atau perangkat...',
                hintStyle: GoogleFonts.inter(
                  fontSize: 12,
                  color: context.textMuted.withValues(alpha: 0.5),
                ),
                prefixIcon: Icon(Icons.search_rounded,
                    color: AppColors.accent.withValues(alpha: 0.7), size: 20),
                filled: true,
                fillColor: context.isDark
                    ? Colors.white.withValues(alpha: 0.05)
                    : Colors.black.withValues(alpha: 0.05),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(16),
                  borderSide: BorderSide.none,
                ),
                contentPadding: const EdgeInsets.symmetric(vertical: 16),
              ),
            ),
            const SizedBox(height: 24),
            Expanded(
              child: Obx(() {
                if (filteredDevices.isEmpty) {
                  return Center(
                    child: Text(
                      'Tidak ada perangkat ditemukan',
                      style: GoogleFonts.inter(color: context.textMuted),
                    ),
                  );
                }
                return ListView.builder(
                  physics: const BouncingScrollPhysics(),
                  itemCount: filteredDevices.length,
                  itemBuilder: (context, index) {
                    final device = filteredDevices[index];
                    final isSelected =
                        homeController.selectedDeviceSlug.value ==
                            device['slug'];

                    return _DevicePickerItem(
                      device: device,
                      isSelected: isSelected,
                      onTap: () {
                        homeController.onDeviceSelected(device);
                        Get.back();
                      },
                    );
                  },
                );
              }),
            ),
          ],
        ),
      ),
      isScrollControlled: true,
    );
  }
}

class _DevicePickerItem extends StatefulWidget {
  final Map<String, dynamic> device;
  final bool isSelected;
  final VoidCallback onTap;

  const _DevicePickerItem({
    required this.device,
    required this.isSelected,
    required this.onTap,
  });

  @override
  State<_DevicePickerItem> createState() => _DevicePickerItemState();
}

class _DevicePickerItemState extends State<_DevicePickerItem> {
  bool _isPressed = false;

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTapDown: (_) => setState(() => _isPressed = true),
      onTapUp: (_) => setState(() => _isPressed = false),
      onTapCancel: () => setState(() => _isPressed = false),
      onTap: widget.onTap,
      behavior: HitTestBehavior.opaque,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 150),
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        decoration: BoxDecoration(
          color: _isPressed
              ? context.dividerColor.withValues(alpha: 0.1)
              : (widget.isSelected
                  ? AppColors.accent.withValues(alpha: 0.05)
                  : context.bgCard),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(
            color: widget.isSelected
                ? AppColors.accent
                : context.dividerColor.withValues(alpha: 0.2),
            width: widget.isSelected ? 1.5 : 1,
          ),
        ),
        child: Row(
          children: [
            Container(
              width: 42,
              height: 42,
              decoration: BoxDecoration(
                color: widget.isSelected
                    ? AppColors.accent
                    : context.dividerColor.withValues(alpha: 0.2),
                shape: BoxShape.circle,
              ),
              child: Icon(
                Icons.location_on_rounded,
                size: 20,
                color: widget.isSelected ? Colors.white : context.textMuted,
              ),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    (widget.device['location'] ?? 'Unknown').toUpperCase(),
                    style: GoogleFonts.rajdhani(
                      fontSize: 16,
                      fontWeight: FontWeight.w800,
                      color: widget.isSelected
                          ? AppColors.accent
                          : context.textPrimary,
                      letterSpacing: 0.5,
                      height: 1.1,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    widget.device['name'] ?? 'Device',
                    style: GoogleFonts.inter(
                      fontSize: 11,
                      fontWeight: FontWeight.w600,
                      color: context.textMuted,
                    ),
                  ),
                ],
              ),
            ),
            Icon(
              widget.isSelected
                  ? Icons.check_circle_rounded
                  : Icons.chevron_right_rounded,
              color: widget.isSelected
                  ? AppColors.accent
                  : context.textMuted.withValues(alpha: 0.3),
              size: 20,
            ),
          ],
        ),
      ),
    );
  }
}
