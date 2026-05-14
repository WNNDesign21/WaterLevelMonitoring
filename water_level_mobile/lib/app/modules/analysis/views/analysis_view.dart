import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:fl_chart/fl_chart.dart';
import 'package:intl/intl.dart';
import '../../../core/theme/app_theme.dart';
import '../../home/controllers/home_controller.dart';
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
            _buildSegmentedControl(context),
            Expanded(
              child: SingleChildScrollView(
                physics: const BouncingScrollPhysics(),
                padding:
                    const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
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

  // ── PREMIUM HEADER ────────────────────────────────────────────────
  Widget _buildPremiumHeader(BuildContext context) {
    return Container(
      padding: const EdgeInsets.fromLTRB(24, 20, 24, 12),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'MONITORING ANALISIS',
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 11,
                    fontWeight: FontWeight.w800,
                    color: AppColors.accent,
                    letterSpacing: 1.2,
                  ),
                ),
                const SizedBox(height: 6),
                Obx(() => GestureDetector(
                      onTap: () => _showDevicePicker(context),
                      behavior: HitTestBehavior.opaque,
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Flexible(
                            child: Text(
                              controller
                                  .homeController.selectedDeviceName.value,
                              style: GoogleFonts.plusJakartaSans(
                                fontSize: 20,
                                fontWeight: FontWeight.w800,
                                color: context.textPrimary,
                                letterSpacing: -0.5,
                              ),
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                          const SizedBox(width: 8),
                          Container(
                            padding: const EdgeInsets.all(2),
                            decoration: BoxDecoration(
                              color: context.dividerColor.withOpacity(0.5),
                              shape: BoxShape.circle,
                            ),
                            child: Icon(Icons.keyboard_arrow_down_rounded,
                                size: 20, color: context.textPrimary),
                          ),
                        ],
                      ),
                    )),
              ],
            ),
          ),
          _headerAction(context, Icons.bar_chart_rounded),
        ],
      ),
    );
  }

  Widget _headerAction(BuildContext context, IconData icon) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: context.bgCard,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: context.borderColor),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.03),
            blurRadius: 10,
            offset: const Offset(0, 4),
          )
        ],
      ),
      child: Icon(icon, color: AppColors.accent, size: 22),
    );
  }

  // ── SEGMENTED CONTROL ─────────────────────────────────────────────
  Widget _buildSegmentedControl(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 18),
      child: LayoutBuilder(builder: (context, constraints) {
        final totalWidth = constraints.maxWidth;
        final itemCount = controller.periods.length;
        final itemWidth = (totalWidth - 8) / itemCount;

        return Container(
          height: 52,
          padding: const EdgeInsets.all(4),
          decoration: BoxDecoration(
            color: context.isDark
                ? const Color(0xFF0F172A)
                : const Color(0xFFF5F8FF),
            borderRadius: BorderRadius.circular(16),
            border: Border.all(
              color: context.isDark
                  ? Colors.white.withOpacity(0.05)
                  : AppColors.accent.withOpacity(0.1),
              width: 1.5,
            ),
          ),
          child: Obx(() {
            final selectedIndex =
                controller.periods.indexOf(controller.selectedPeriod.value);

            return Stack(
              children: [
                // Sliding Indicator
                AnimatedPositioned(
                  duration: const Duration(milliseconds: 400),
                  curve: Curves.easeInOutBack,
                  left: selectedIndex * itemWidth,
                  width: itemWidth,
                  height: 44,
                  child: Container(
                    decoration: BoxDecoration(
                      color: context.isDark
                          ? const Color(0xFF1E293B)
                          : Colors.white,
                      borderRadius: BorderRadius.circular(12),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.08),
                          blurRadius: 8,
                          offset: const Offset(0, 3),
                        )
                      ],
                    ),
                  ),
                ),

                // Labels Row
                Row(
                  children: controller.periods.asMap().entries.map((entry) {
                    final index = entry.key;
                    final period = entry.value;
                    final isSelected =
                        controller.selectedPeriod.value == period;
                    final isCustom = period == 'Custom';

                    return Expanded(
                      child: Row(
                        children: [
                          if (isCustom)
                            Container(
                              width: 1.5,
                              height: 18,
                              margin: const EdgeInsets.only(right: 4),
                              decoration: BoxDecoration(
                                color: context.dividerColor.withOpacity(0.5),
                                borderRadius: BorderRadius.circular(10),
                              ),
                            ),
                          Expanded(
                            child: GestureDetector(
                              onTap: () {
                                if (isCustom) {
                                  controller.selectDateRange(context);
                                } else {
                                  controller.changePeriod(period);
                                }
                              },
                              behavior: HitTestBehavior.opaque,
                              child: AnimatedContainer(
                                duration: const Duration(milliseconds: 300),
                                alignment: Alignment.center,
                                decoration: BoxDecoration(
                                  color: isCustom && !isSelected
                                      ? (context.isDark
                                          ? Colors.white.withOpacity(0.05)
                                          : Colors.white)
                                      : Colors.transparent,
                                  borderRadius: BorderRadius.circular(10),
                                ),
                                child: AnimatedDefaultTextStyle(
                                  duration: const Duration(milliseconds: 300),
                                  style: GoogleFonts.plusJakartaSans(
                                    fontSize: 10,
                                    fontWeight: isSelected
                                        ? FontWeight.w800
                                        : FontWeight.w600,
                                    color: isSelected
                                        ? AppColors.accent
                                        : context.textSecondary,
                                    letterSpacing: 0,
                                  ),
                                  child: isCustom
                                      ? Icon(Icons.calendar_month_rounded,
                                          size: 18,
                                          color: isSelected
                                              ? AppColors.accent
                                              : context.textSecondary)
                                      : FittedBox(
                                          fit: BoxFit.scaleDown,
                                          child: Padding(
                                            padding: const EdgeInsets.symmetric(
                                                horizontal: 4),
                                            child: Text(period.toUpperCase()),
                                          ),
                                        ),
                                ),
                              ),
                            ),
                          ),
                        ],
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
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: context.borderColor, width: 1.5),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(context.isDark ? 0.3 : 0.04),
            blurRadius: 30,
            offset: const Offset(0, 10),
          )
        ],
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
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 10,
                        fontWeight: FontWeight.w800,
                        color: context.textMuted,
                        letterSpacing: 1.2,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Obx(() => Text(
                          controller.periodLabel.toUpperCase(),
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 16,
                            fontWeight: FontWeight.w800,
                            color: context.textPrimary,
                            letterSpacing: -0.5,
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
                        style: GoogleFonts.plusJakartaSans(
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
      padding: const EdgeInsets.symmetric(vertical: 24, horizontal: 8),
      decoration: BoxDecoration(
        color: context.isDark
            ? Colors.white.withOpacity(0.02)
            : Colors.black.withOpacity(0.01),
        borderRadius: const BorderRadius.vertical(bottom: Radius.circular(24)),
      ),
      child: Column(
        children: [
          Row(
            children: [
              Expanded(
                child: Obx(() => _buildModernMiniStat(
                      context,
                      'MINIMUM',
                      controller.minLevel.value.toStringAsFixed(2),
                      Icons.arrow_downward_rounded,
                      const Color(0xFF3B82F6),
                    )),
              ),
              _statDivider(context),
              Expanded(
                child: Obx(() => _buildModernMiniStat(
                      context,
                      'MAKSIMUM',
                      controller.maxLevel.value.toStringAsFixed(2),
                      Icons.arrow_upward_rounded,
                      const Color(0xFFEF4444),
                    )),
              ),
            ],
          ),
          Padding(
            padding: const EdgeInsets.symmetric(vertical: 20),
            child: Divider(
                color: context.dividerColor.withOpacity(0.4),
                height: 1,
                indent: 24,
                endIndent: 24),
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
                      unit: ' jam',
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
            context.dividerColor.withOpacity(0),
            context.dividerColor.withOpacity(0.6),
            context.dividerColor.withOpacity(0),
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
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(4),
                decoration: BoxDecoration(
                  color: color.withOpacity(0.15),
                  shape: BoxShape.circle,
                ),
                child: Icon(icon, color: color, size: 12),
              ),
              const SizedBox(width: 8),
              Text(
                label,
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 10,
                  fontWeight: FontWeight.w800,
                  color: context.textMuted,
                  letterSpacing: 0.5,
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          RichText(
            text: TextSpan(
              children: [
                TextSpan(
                  text: value,
                  style: GoogleFonts.rajdhani(
                    fontSize: 28,
                    fontWeight: FontWeight.w800,
                    color: context.textPrimary,
                    height: 1,
                  ),
                ),
                TextSpan(
                  text: unit,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 12,
                    fontWeight: FontWeight.w700,
                    color: context.textSecondary,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildRefinedChart(BuildContext context) {
    return LineChart(
      LineChartData(
        gridData: FlGridData(
          show: true,
          drawVerticalLine: false,
          horizontalInterval: 0.5,
          getDrawingHorizontalLine: (value) => FlLine(
            color: context.dividerColor.withOpacity(0.4),
            strokeWidth: 1,
            dashArray: [8, 4],
          ),
        ),
        titlesData: FlTitlesData(
          rightTitles:
              const AxisTitles(sideTitles: SideTitles(showTitles: false)),
          topTitles:
              const AxisTitles(sideTitles: SideTitles(showTitles: false)),
          bottomTitles: AxisTitles(
            sideTitles: SideTitles(
              showTitles: true,
              reservedSize: 32,
              interval: 6,
              getTitlesWidget: (value, meta) {
                return Padding(
                  padding: const EdgeInsets.only(top: 12),
                  child: Text(
                    '${value.toInt()}:00',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 10,
                      color: context.textMuted,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                );
              },
            ),
          ),
          leftTitles: AxisTitles(
            sideTitles: SideTitles(
              showTitles: true,
              reservedSize: 44,
              interval: 0.5,
              getTitlesWidget: (value, meta) {
                return Text(
                  value.toStringAsFixed(1),
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 10,
                    color: context.textMuted,
                    fontWeight: FontWeight.w700,
                  ),
                );
              },
            ),
          ),
        ),
        borderData: FlBorderData(show: false),
        minY: 0.8,
        maxY: 2.2,
        lineBarsData: [
          LineChartBarData(
            spots: List.generate(
                24, (i) => FlSpot(i.toDouble(), 1.2 + (i % 5) * 0.1)),
            isCurved: true,
            curveSmoothness: 0.35,
            preventCurveOverShooting: true,
            gradient: const LinearGradient(
              colors: [AppColors.accent, Color(0xFF6366F1)],
              begin: Alignment.centerLeft,
              end: Alignment.centerRight,
            ),
            barWidth: 4,
            isStrokeCapRound: true,
            dotData: const FlDotData(show: false),
            belowBarData: BarAreaData(
              show: true,
              gradient: LinearGradient(
                colors: [
                  AppColors.accent.withOpacity(0.2),
                  AppColors.accent.withOpacity(0.0),
                ],
                begin: Alignment.topCenter,
                end: Alignment.bottomCenter,
              ),
            ),
          ),
        ],
      ),
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
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 12,
                  fontWeight: FontWeight.w800,
                  color: context.textPrimary,
                  letterSpacing: 0.5,
                ),
              ),
              Text(
                'Lihat Semua',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 11,
                  fontWeight: FontWeight.w700,
                  color: AppColors.accent,
                ),
              ),
            ],
          ),
        ),
        Container(
          decoration: BoxDecoration(
            color: context.bgCard,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: context.borderColor),
          ),
          child: Obx(() {
            if (controller.historyData.isEmpty) {
              return Padding(
                padding: const EdgeInsets.all(40),
                child: Center(
                  child: Column(
                    children: [
                      Icon(Icons.history_rounded,
                          size: 48, color: context.dividerColor),
                      const SizedBox(height: 16),
                      Text(
                        'Belum ada data log',
                        style: GoogleFonts.plusJakartaSans(
                          color: context.textMuted,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                ),
              );
            }
            return ListView.separated(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              itemCount: controller.historyData.length > 8
                  ? 8
                  : controller.historyData.length,
              separatorBuilder: (context, index) => Divider(
                  height: 1, color: context.dividerColor.withOpacity(0.5)),
              itemBuilder: (context, index) {
                final data = controller.historyData[index];
                return _buildModernHistoryItem(context, data);
              },
            );
          }),
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
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 18),
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
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 13,
                    fontWeight: FontWeight.w800,
                    color: context.textPrimary,
                  ),
                ),
                Text(
                  'WIB',
                  style: GoogleFonts.plusJakartaSans(
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
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: context.textSecondary,
                      ),
                    ),
                  ],
                ),
                Text(
                  date,
                  style: GoogleFonts.plusJakartaSans(
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
              boxShadow: [
                BoxShadow(
                  color: color.withOpacity(0.4),
                  blurRadius: 4,
                  spreadRadius: 1,
                )
              ],
            ),
          ),
          const SizedBox(width: 8),
          Text(
            status.toUpperCase(),
            style: GoogleFonts.plusJakartaSans(
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
            style: GoogleFonts.plusJakartaSans(
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
          borderRadius: const BorderRadius.vertical(top: Radius.circular(28)),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.2),
              blurRadius: 20,
              offset: const Offset(0, -5),
            )
          ],
        ),
        child: Column(
          children: [
            Container(
              width: 50,
              height: 5,
              decoration: BoxDecoration(
                color: context.dividerColor.withOpacity(0.6),
                borderRadius: BorderRadius.circular(10),
              ),
            ),
            const SizedBox(height: 24),
            Text(
              'Pilih Lokasi Pemantauan',
              style: GoogleFonts.plusJakartaSans(
                fontSize: 18,
                fontWeight: FontWeight.w800,
                color: context.textPrimary,
                letterSpacing: -0.5,
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
              style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w600),
              decoration: InputDecoration(
                hintText: 'Cari lokasi atau nama perangkat...',
                prefixIcon:
                    const Icon(Icons.search_rounded, color: AppColors.accent),
                filled: true,
                fillColor: context.isDark
                    ? Colors.white.withOpacity(0.05)
                    : Colors.black.withOpacity(0.04),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(20),
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
                      style:
                          GoogleFonts.plusJakartaSans(color: context.textMuted),
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

                    return Container(
                      margin: const EdgeInsets.only(bottom: 14),
                      decoration: BoxDecoration(
                        color: isSelected
                            ? AppColors.accent.withOpacity(0.08)
                            : Colors.transparent,
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(
                          color: isSelected
                              ? AppColors.accent
                              : context.dividerColor.withOpacity(0.5),
                          width: isSelected ? 1.5 : 1,
                        ),
                      ),
                      child: ListTile(
                        onTap: () {
                          homeController.onDeviceSelected(device);
                          Get.back();
                        },
                        contentPadding: const EdgeInsets.symmetric(
                            horizontal: 20, vertical: 8),
                        leading: Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: isSelected
                                ? AppColors.accent
                                : context.dividerColor.withOpacity(0.4),
                            borderRadius: BorderRadius.circular(14),
                          ),
                          child: Icon(
                            Icons.location_on_rounded,
                            size: 22,
                            color:
                                isSelected ? Colors.white : context.textMuted,
                          ),
                        ),
                        title: Text(
                          (device['location'] ?? 'Unknown').toUpperCase(),
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 14,
                            fontWeight: FontWeight.w800,
                            color: isSelected
                                ? AppColors.accent
                                : context.textPrimary,
                          ),
                        ),
                        subtitle: Padding(
                          padding: const EdgeInsets.only(top: 4),
                          child: Text(
                            device['name'] ?? 'Device',
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 12,
                              fontWeight: FontWeight.w600,
                              color: context.textSecondary,
                            ),
                          ),
                        ),
                        trailing: isSelected
                            ? const Icon(Icons.check_circle_rounded,
                                color: AppColors.accent)
                            : null,
                      ),
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
