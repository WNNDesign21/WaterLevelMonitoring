
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart' hide Path;
import 'package:fl_chart/fl_chart.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/theme/theme_controller.dart';
import '../../../routes/app_pages.dart';
import '../controllers/home_controller.dart';
import 'widgets/flood_scene_painter.dart';
import 'widgets/home_skeletons.dart';

class HomeView extends GetView<HomeController> {
  const HomeView({super.key});

  @override
  Widget build(BuildContext context) {
    final themeController = Get.find<ThemeController>();

    return Scaffold(
      backgroundColor: context.bgPrimary,
      body: Stack(
        children: [
          // ── Background Glows ────────────────────────────────────────
          _buildBackgroundDecoration(context),

          SafeArea(
            bottom: false,
            child: LayoutBuilder(
              builder: (context, constraints) {
                return Stack(
              children: [
                // ── Scrollable Content (STAYS STILL) ─────────────────────────
                NotificationListener<ScrollNotification>(
                  onNotification: (notification) {
                    if (notification is OverscrollNotification) {
                      // Mencegah konten ikut tertarik (bouncing)
                      if (notification.overscroll < 0 &&
                          !controller.isManualRefreshing.value) {
                        controller.pullDistance.value += -notification.overscroll * 0.5;
                      }
                    } else if (notification is ScrollUpdateNotification) {
                      controller.scrollOffset.value = notification.metrics.pixels;
                      // Saat pengguna scroll ke bawah tapi masih menahan tarikan
                      if (notification.scrollDelta != null && controller.pullDistance.value > 0) {
                        controller.pullDistance.value -= notification.scrollDelta!;
                        if (controller.pullDistance.value < 0) {
                          controller.pullDistance.value = 0.0;
                        }
                      }
                    } else if (notification is ScrollEndNotification) {
                      if (controller.pullDistance.value > 60 &&
                          !controller.isManualRefreshing.value) {
                        controller.onManualRefresh();
                      } else if (!controller.isManualRefreshing.value) {
                        controller.pullDistance.value = 0.0;
                      }
                    }
                    return false;
                  },
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(parent: ClampingScrollPhysics()),
                    child: Obx(() {
                      // Jika sedang loading dan belum ada data, tampilkan Skeleton di seluruh halaman
                      if (controller.isLoading.value && controller.devices.isEmpty) {
                        return const HomeSkeletons(showScene: true);
                      }

                      // Tampilan utama saat data sudah ada
                      return Stack(
                        children: [
                          _buildImmersiveScene(context),
                          Padding(
                            padding: const EdgeInsets.only(top: 436),
                            child: Container(
                              width: double.infinity,
                              constraints: BoxConstraints(
                                minHeight: constraints.maxHeight - 436,
                              ),
                              decoration: BoxDecoration(
                                color: context.bgPrimary,
                                borderRadius: const BorderRadius.only(
                                  topLeft: Radius.circular(32),
                                  topRight: Radius.circular(32),
                                ),
                              ),
                              child: controller.devices.isEmpty
                                  ? _buildEmptyState(context)
                                  : Padding(
                                      padding: const EdgeInsets.fromLTRB(20, 24, 20, 40),
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          _buildSceneMetricsRow(context),
                                          const SizedBox(height: 24),
                                          _buildInsightsSection(context),
                                          const SizedBox(height: 24),
                                          _buildSparklineSection(context),
                                          const SizedBox(height: 24),
                                          _buildSystemStatus(context),
                                          const SizedBox(height: 24),
                                          _buildMapSection(context),
                                        ],
                                      ),
                                    ),
                            ),
                          ),
                        ],
                      );
                    }),
                  ),
                ),



                // ── Fixed Top Bar & Warnings (The "Frame") ─────────────
                Positioned(
                  top: 0,
                  left: 0,
                  right: 0,
                  child: Column(
                    children: [
                      Obx(() => controller.hasInternet.value
                          ? const SizedBox.shrink()
                          : Container(
                              width: double.infinity,
                              padding: const EdgeInsets.symmetric(
                                  vertical: 8, horizontal: 16),
                              color: Colors.red.shade600,
                              child: const Text(
                                'Tidak Ada Koneksi Internet',
                                style: TextStyle(
                                    color: Colors.white,
                                    fontSize: 12,
                                    fontWeight: FontWeight.bold),
                                textAlign: TextAlign.center,
                              ),
                            )),
                      Obx(() {
                        final offset = controller.scrollOffset.value;
                        final opacity = (offset / 100).clamp(0.0, 1.0);
                        
                        return Container(
                          decoration: BoxDecoration(
                            color: context.bgPrimary.withValues(alpha: opacity),
                            border: Border(
                              bottom: BorderSide(
                                color: context.borderColor.withValues(alpha: opacity),
                                width: 1,
                              ),
                            ),
                          ),
                          child: _buildTopBar(context, themeController),
                        );
                      }),
                    ],
                  ),
                ),

                // ── Floating Refresh Icon (Front Layer) ───────────────
                Obx(() {
                  final show = controller.pullDistance.value > 5 ||
                      controller.isManualRefreshing.value;
                  if (!show) return const SizedBox.shrink();

                  final top = controller.isManualRefreshing.value
                      ? 60.0
                      : (-40.0 + controller.pullDistance.value).clamp(-40.0, 100.0);

                  return Positioned(
                    top: top,
                    left: 0,
                    right: 0,
                    child: Center(
                      child: Container(
                        padding: const EdgeInsets.all(10),
                        decoration: BoxDecoration(
                          color: context.bgCard,
                          shape: BoxShape.circle,
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withValues(alpha: 0.1),
                              blurRadius: 12,
                              offset: const Offset(0, 4),
                            )
                          ],
                        ),
                        child: controller.isManualRefreshing.value
                            ? const SizedBox(
                                width: 20,
                                height: 20,
                                child: CircularProgressIndicator(
                                  strokeWidth: 2,
                                  color: AppColors.accent,
                                ),
                              )
                            : Transform.rotate(
                                angle: controller.pullDistance.value * 0.05,
                                child: const Icon(
                                  Icons.refresh,
                                  color: AppColors.accent,
                                  size: 22,
                                ),
                              ),
                      ),
                    ),
                  );
                }),
              ],
            );
          },
        ),
      ),
    ],
  ),
      bottomNavigationBar: _buildBottomNav(context),
    );
  }

  // Separated widgets for cleaner build method
  Widget _buildEmptyState(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 48, 20, 40),
      child: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.device_unknown_rounded,
                size: 64, color: context.textMuted.withValues(alpha: 0.5)),
            const SizedBox(height: 16),
            Text(
              'Tidak Ada Perangkat',
              style: GoogleFonts.inter(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: context.textPrimary),
            ),
            const SizedBox(height: 8),
            Text(
              'Belum ada titik sensor yang terdaftar atau dapat diakses.',
              textAlign: TextAlign.center,
              style: GoogleFonts.inter(fontSize: 14, color: context.textMuted),
            ),
            const SizedBox(height: 24),
            ElevatedButton(
              onPressed: () => controller.fetchDevices(),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.accent,
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12)),
              ),
              child: const Text('Coba Lagi'),
            )
          ],
        ),
      ),
    );
  }


  // ═══════════════════════════════════════════════════════════════════
  // DECORATIONS
  // ═══════════════════════════════════════════════════════════════════

  Widget _buildBackgroundDecoration(BuildContext context) {
    return Stack(
      children: [
        Positioned(
          top: -150,
          right: -100,
          child: Container(
            width: 400,
            height: 400,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              gradient: RadialGradient(
                colors: [
                  AppColors.accent
                      .withValues(alpha: context.isDark ? 0.15 : 0.08),
                  Colors.transparent,
                ],
              ),
            ),
          ),
        ),
        Positioned(
          bottom: -100,
          left: -100,
          child: Container(
            width: 300,
            height: 300,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              gradient: RadialGradient(
                colors: [
                  AppColors.statusSafe
                      .withValues(alpha: context.isDark ? 0.1 : 0.05),
                  Colors.transparent,
                ],
              ),
            ),
          ),
        ),
      ],
    );
  }

  // ═══════════════════════════════════════════════════════════════════
  // TOP BAR (DROPDOWN + THEME)
  // ═══════════════════════════════════════════════════════════════════

  Widget _buildTopBar(BuildContext context, ThemeController themeController) {
    return Obx(() {
      final offset = controller.scrollOffset.value;
      final opacity = (offset / 100).clamp(0.0, 1.0);
      
      // Dynamic colors based on scroll
      final textColor = Color.lerp(Colors.white, context.textPrimary, opacity);
      final subColor = Color.lerp(Colors.white.withValues(alpha: 0.6), context.textSecondary, opacity);
      final iconColor = Color.lerp(Colors.white, AppColors.accent, opacity);
      final buttonBg = Color.lerp(Colors.white.withValues(alpha: 0.25), context.bgCard, opacity);

      return Padding(
        padding: const EdgeInsets.fromLTRB(20, 12, 12, 12),
        child: Row(
          children: [
            // Device Selector
            Expanded(
              child: Obx(() {
                final selectedSlug = controller.selectedDeviceSlug.value;
                final selectedDevice = controller.devices.firstWhere(
                  (d) => d['slug'] == selectedSlug,
                  orElse: () => {},
                );
                
                final location = selectedDevice['location'] ?? 'PILIH LOKASI';
                final name = selectedDevice['name'] ?? 'Sentuh untuk memilih node...';

                return GestureDetector(
                  onTap: () => _showDevicePicker(context),
                  behavior: HitTestBehavior.opaque,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text(
                        location.toUpperCase(),
                        style: GoogleFonts.inter(
                          fontSize: 14,
                          fontWeight: FontWeight.w900,
                          color: textColor,
                          letterSpacing: 0.5,
                          height: 1,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Row(
                        children: [
                          Text(
                            name,
                            style: GoogleFonts.inter(
                              fontSize: 10,
                              fontWeight: FontWeight.w600,
                              color: subColor,
                              letterSpacing: 0.2,
                            ),
                          ),
                          const SizedBox(width: 4),
                          Icon(Icons.keyboard_arrow_down_rounded,
                              color: iconColor, size: 14),
                        ],
                      ),
                    ],
                  ),
                );
              }),
            ),

            const SizedBox(width: 12),

            // User Profile (Guest)
            GestureDetector(
              onTap: () => Get.toNamed(Routes.SETTINGS),
              child: Container(
                padding: const EdgeInsets.fromLTRB(16, 6, 6, 6),
                decoration: BoxDecoration(
                  color: buttonBg,
                  borderRadius: BorderRadius.circular(100),
                ),
                child: Row(
                  children: [
                    Text(
                      'GUEST',
                      style: GoogleFonts.inter(
                        fontSize: 11,
                        fontWeight: FontWeight.w800,
                        color: textColor,
                        letterSpacing: 0.5,
                      ),
                    ),
                    const SizedBox(width: 10),
                    Container(
                      padding: const EdgeInsets.all(6),
                      decoration: BoxDecoration(
                        color: textColor!.withValues(alpha: 0.15),
                        shape: BoxShape.circle,
                      ),
                      child: Icon(
                        Icons.person_outline_rounded,
                        color: textColor,
                        size: 16,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      );
    });
  }

  // ═══════════════════════════════════════════════════════════════════
  // IMMERSIVE FLOOD SCENE (UNIFIED)
  // ═══════════════════════════════════════════════════════════════════

  Widget _buildImmersiveScene(BuildContext context) {
    return Obx(() {
      final double maxMdpl = controller.elevationMdpl.value -
          (controller.sensorToBank.value / 100.0);
      final double minMdpl = maxMdpl - (controller.riverDepth.value / 100.0);
      final double range = maxMdpl - minMdpl;
      final double fill = (range > 0)
          ? ((controller.waterLevel.value - minMdpl) / range).clamp(0.0, 1.0)
          : 0.0;
      final Color statusColor = Color(controller.statusColor.value);
      final bool dark = context.isDark;

      return Container(
        height: 500,
        width: double.infinity,
        child: CustomPaint(
          painter: FloodScenePainter(
            fillRatio: fill,
            wavePhase: controller.wavePhase.value,
            waterColor: statusColor,
            statusColor: statusColor,
            isDark: dark,
            weatherCode: controller.weatherCode.value,
            tempC: controller.weatherTemp.value,
            locationName: controller.weatherLocationName.value,
            weatherDesc: controller.weatherDesc.value,
            cityImage: controller.cityImg.value,
            statusText: controller.statusSiaga.value,
            waterLevel: controller.waterLevel.value,
            elevationMdpl: controller.elevationMdpl.value,
            sensorToBank: controller.sensorToBank.value,
            riverDepth: controller.riverDepth.value,
            showEnvironment: true,
            showTank: true,
          ),
        ),
      );
    });
  }

  // ═══════════════════════════════════════════════════════════════════
  // METRICS & STATUS
  // ═══════════════════════════════════════════════════════════════════

  Widget _buildSceneMetricsRow(BuildContext context) {
    return Obx(() {
      final double maxMdpl = controller.elevationMdpl.value -
          (controller.sensorToBank.value / 100.0);
      final double minMdpl = maxMdpl - (controller.riverDepth.value / 100.0);
      final double range = maxMdpl - minMdpl;
      final double fill = (range > 0)
          ? ((controller.waterLevel.value - minMdpl) / range).clamp(0.0, 1.0)
          : 0.0;
      final Color statusColor = Color(controller.statusColor.value);

      return Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Text(
                'PEMANTAUAN REAL-TIME',
                style: GoogleFonts.inter(
                  fontSize: 8.5,
                  fontWeight: FontWeight.w800,
                  color: AppColors.accent.withValues(alpha: 0.8),
                  letterSpacing: 1.2,
                ),
              ),
              const Spacer(),
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: statusColor.withValues(alpha: 0.12),
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: statusColor.withValues(alpha: 0.3)),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Container(
                      width: 6,
                      height: 6,
                      decoration: BoxDecoration(
                        color: statusColor,
                        shape: BoxShape.circle,
                      ),
                    ),
                    const SizedBox(width: 6),
                    Text(
                      controller.statusSiaga.value,
                      style: GoogleFonts.inter(
                        fontSize: 10,
                        fontWeight: FontWeight.w800,
                        color: statusColor,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(
                child: _buildSceneMetric(
                  context,
                  icon: Icons.water,
                  label: 'Tinggi Air',
                  value: '${controller.waterLevel.value.toStringAsFixed(2)} m',
                  color: statusColor,
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: _buildSceneMetric(
                  context,
                  icon: Icons.percent_rounded,
                  label: 'Level',
                  value: '${(fill * 100).toInt()}%',
                  color: statusColor,
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: _buildSceneMetric(
                  context,
                  icon: Icons.waves_rounded,
                  label: 'Laju Arus',
                  value: controller.flowVelocity.value.toStringAsFixed(2),
                  unit: ' m',
                  color: controller.flowTrend.value > 0 
                        ? AppColors.statusSiaga1 
                        : (controller.flowTrend.value < 0 ? AppColors.statusSafe : AppColors.accent),
                ),
              ),
            ],
          ),
        ],
      );
    });
  }

  Widget _buildSceneMetric(
    BuildContext context, {
    required IconData icon,
    required String label,
    required String value,
    String unit = '',
    required Color color,
  }) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(12),
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            context.bgCard,
            context.isDark
                ? context.bgCard.withValues(alpha: 0.8)
                : const Color(0xFFF1F5FB),
          ],
        ),
        boxShadow: AppShadows.card(context.isDark),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(icon, color: color, size: 16),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  label.toUpperCase(),
                  style: GoogleFonts.inter(
                    fontSize: 8,
                    fontWeight: FontWeight.w800,
                    color: context.textMuted,
                    letterSpacing: 0.5,
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          RichText(
            text: TextSpan(
              children: [
                TextSpan(
                  text: value,
                  style: GoogleFonts.rajdhani(
                    fontSize: 20,
                    fontWeight: FontWeight.w800,
                    color: context.textPrimary,
                    height: 1,
                  ),
                ),
                if (unit.isNotEmpty)
                  TextSpan(
                    text: unit,
                    style: GoogleFonts.inter(
                      fontSize: 10,
                      fontWeight: FontWeight.w600,
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

  // ═══════════════════════════════════════════════════════════════════
  // DEEP INSIGHTS SECTION
  // ═══════════════════════════════════════════════════════════════════

  Widget _buildInsightsSection(BuildContext context) {
    return Obx(() {
      final Color statusColor = Color(controller.statusColor.value);

      return Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.only(left: 4, bottom: 12),
            child: Text(
              'ANALISIS REAL-TIME',
              style: GoogleFonts.inter(
                fontSize: 8.5,
                fontWeight: FontWeight.w800,
                color: AppColors.accent.withValues(alpha: 0.8),
                letterSpacing: 1.2,
              ),
            ),
          ),
          GridView.count(
            crossAxisCount: 2,
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            mainAxisSpacing: 16,
            crossAxisSpacing: 16,
            childAspectRatio: 1.35,
            children: [
              _buildInsightCard(
                context,
                title: 'JARAK AMAN',
                value: controller.distanceToGround.value.toStringAsFixed(1),
                unit: 'cm',
                icon: Icons.straighten_rounded,
                color: statusColor,
                subtitle: controller.distanceToGround.value < 0
                    ? 'Waspada Luber'
                    : 'Tinggi Aman',
              ),
              _buildInsightCard(
                context,
                title: 'ESTIMASI AI',
                value: controller.etaOverflow.value,
                unit: '',
                icon: Icons.auto_graph_rounded,
                color: AppColors.statusSiaga2,
                subtitle: 'Prediksi Meluap',
              ),
              _buildInsightCard(
                context,
                title: 'TREN AIR',
                value: controller.flowTrend.value > 0 
                      ? "NAIK" 
                      : (controller.flowTrend.value < 0 ? "TURUN" : "STABIL"),
                unit: '',
                icon: controller.flowTrend.value > 0 
                      ? Icons.trending_up_rounded 
                      : (controller.flowTrend.value < 0 ? Icons.trending_down_rounded : Icons.trending_flat_rounded),
                color: controller.flowTrend.value > 0 
                      ? AppColors.statusSiaga1 
                      : (controller.flowTrend.value < 0 ? AppColors.statusSafe : AppColors.accent),
                subtitle: 'Aktivitas Permukaan',
              ),
              _buildInsightCard(
                context,
                title: 'JARAK SENSOR',
                value: controller.distance.value.toStringAsFixed(1),
                unit: 'cm',
                icon: Icons.sensors_rounded,
                color: AppColors.accent,
                subtitle: 'Raw Data Sensor',
              ),
            ],
          ),
        ],
      );
    });
  }

  Widget _buildInsightCard(
    BuildContext context, {
    required String title,
    required String value,
    required String unit,
    required IconData icon,
    required Color color,
    required String subtitle,
  }) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(12),
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            context.bgCard,
            context.isDark
                ? context.bgCard.withValues(alpha: 0.8)
                : const Color(0xFFF1F5FB),
          ],
        ),
        boxShadow: AppShadows.card(context.isDark),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisSize: MainAxisSize.min,
        children: [
          Row(
            children: [
              Icon(icon, color: color, size: 18),
              const SizedBox(width: 10),
              Expanded(
                child: Text(
                  title.toUpperCase(),
                  style: GoogleFonts.inter(
                    fontSize: 8,
                    fontWeight: FontWeight.w800,
                    color: context.textMuted,
                    letterSpacing: 0.8,
                  ),
                ),
              ),
              Container(
                width: 6,
                height: 6,
                decoration: BoxDecoration(
                  color: color.withValues(alpha: 0.4),
                  shape: BoxShape.circle,
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Row(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(
                value,
                style: GoogleFonts.rajdhani(
                  fontSize: 24,
                  fontWeight: FontWeight.w900,
                  color: context.textPrimary,
                  height: 1,
                ),
              ),
              if (unit.isNotEmpty) ...[
                const SizedBox(width: 4),
                Padding(
                  padding: const EdgeInsets.only(bottom: 2),
                  child: Text(
                    unit,
                    style: GoogleFonts.inter(
                      fontSize: 10,
                      fontWeight: FontWeight.w700,
                      color: context.textSecondary,
                    ),
                  ),
                ),
              ],
            ],
          ),
          const SizedBox(height: 4),
          Text(
            subtitle,
            style: GoogleFonts.inter(
              fontSize: 9,
              fontWeight: FontWeight.w500,
              color: context.textSecondary,
              letterSpacing: 0.2,
            ),
          ),
        ],
      ),
    );
  }

  // ═══════════════════════════════════════════════════════════════════
  // MAP SECTION
  // ═══════════════════════════════════════════════════════════════════

  Widget _buildMapSection(BuildContext context) {
    return Obx(() {
      if (controller.selectedDeviceLat.value == 0)
        return const SizedBox.shrink();

      final point = LatLng(controller.selectedDeviceLat.value,
          controller.selectedDeviceLng.value);

      return Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.only(left: 4, bottom: 12),
            child: Text(
              'LOKASI PERANGKAT',
              style: GoogleFonts.inter(
                fontSize: 8.5,
                fontWeight: FontWeight.w800,
                color: AppColors.accent.withValues(alpha: 0.8),
                letterSpacing: 1.2,
              ),
            ),
          ),
          Container(
            height: 200,
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(12),
              border: Border.all(
                color: context.dividerColor.withValues(alpha: 0.3),
                width: 1,
              ),
            ),
            clipBehavior: Clip.antiAlias,
            child: Stack(
              children: [
                FlutterMap(
                  options: MapOptions(
                    initialCenter: point,
                    initialZoom: 15,
                    interactionOptions:
                        const InteractionOptions(flags: InteractiveFlag.none),
                  ),
                  children: [
                    TileLayer(
                      urlTemplate: context.isDark
                          ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
                          : 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
                      subdomains: const ['a', 'b', 'c', 'd'],
                      userAgentPackageName: 'com.cybernova.waterlevelmonitoring',
                    ),
                    MarkerLayer(
                      markers: [
                        Marker(
                          point: point,
                          width: 40,
                          height: 40,
                          child: const Icon(Icons.location_on,
                              color: AppColors.statusSiaga1, size: 40),
                        ),
                      ],
                    ),
                  ],
                ),
                // Radial Inner Shadow (Vignette Effect)
                IgnorePointer(
                  child: Container(
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(12),
                      gradient: RadialGradient(
                        center: Alignment.center,
                        radius: 1.1,
                        colors: [
                          Colors.transparent,
                          Colors.black.withValues(alpha: context.isDark ? 0.25 : 0.15),
                        ],
                        stops: const [0.7, 1.0],
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      );
    });
  }

  // ═══════════════════════════════════════════════════════════════════
  // SYSTEM STATUS
  // ═══════════════════════════════════════════════════════════════════

  Widget _buildSystemStatus(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(12),
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            context.bgCard,
            context.isDark
                ? context.bgCard.withValues(alpha: 0.8)
                : const Color(0xFFF1F5FB),
          ],
        ),
        boxShadow: AppShadows.card(context.isDark),
      ),
      child: Column(
        children: [
          Row(
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'UPDATE TERAKHIR',
                      style: GoogleFonts.inter(
                        fontSize: 9,
                        fontWeight: FontWeight.w800,
                        color: context.textMuted,
                      ),
                    ),
                    Obx(() => Text(
                          controller.lastUpdated.value.isNotEmpty
                              ? controller.lastUpdated.value
                              : '-- : --',
                          style: GoogleFonts.inter(
                            fontSize: 13,
                            fontWeight: FontWeight.w600,
                            color: context.textPrimary,
                          ),
                        )),
                  ],
                ),
              ),
              Obx(() => Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                    decoration: BoxDecoration(
                      color: controller.isOnline.value
                          ? AppColors.statusSafe.withValues(alpha: 0.1)
                          : AppColors.statusSiaga1.withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Text(
                      controller.isOnline.value ? 'ONLINE' : 'OFFLINE',
                      style: GoogleFonts.inter(
                        fontSize: 10,
                        fontWeight: FontWeight.bold,
                        color: controller.isOnline.value
                            ? AppColors.statusSafe
                            : AppColors.statusSiaga1,
                      ),
                    ),
                  )),
            ],
          ),
          const SizedBox(height: 20),
          _buildWeatherAndSignalRow(context),
          const SizedBox(height: 20),
          Divider(color: context.dividerColor.withValues(alpha: 0.5), height: 1),
          const SizedBox(height: 20),
          Obx(() => Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              _buildStatItem(
                context,
                label: 'RATA-RATA (24J)',
                value: '${controller.avgWaterLevel.value.toStringAsFixed(2)} m',
                icon: Icons.analytics_outlined,
                color: AppColors.accent,
              ),
              _buildStatItem(
                context,
                label: 'TERTINGGI',
                value: '${controller.maxWaterLevel.value.toStringAsFixed(2)} m',
                icon: Icons.trending_up_rounded,
                color: AppColors.statusSiaga1,
              ),
              _buildStatItem(
                context,
                label: 'TERENDAH',
                value: '${controller.minWaterLevel.value.toStringAsFixed(2)} m',
                icon: Icons.trending_down_rounded,
                color: AppColors.statusSafe,
              ),
            ],
          )),
        ],
      ),
    );
  }

Widget _buildStatItem(BuildContext context, {required String label, required String value, required IconData icon, required Color color}) {
  return Column(
    crossAxisAlignment: CrossAxisAlignment.start,
    children: [
      Row(
        children: [
          Icon(icon, size: 12, color: color),
          const SizedBox(width: 4),
          Text(
            label,
            style: GoogleFonts.inter(
              fontSize: 8,
              fontWeight: FontWeight.w800,
              color: context.textMuted,
            ),
          ),
        ],
      ),
      const SizedBox(height: 4),
      Text(
        value,
        style: GoogleFonts.rajdhani(
          fontSize: 14,
          fontWeight: FontWeight.w800,
          color: context.textPrimary,
        ),
      ),
    ],
  );
}
  // ═══════════════════════════════════════════════════════════════════
  // NEW SECTIONS (SPARKLINE, SIGNAL & WEATHER)
  // ═══════════════════════════════════════════════════════════════════

  Widget _buildSparklineSection(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(12),
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            context.bgCard,
            context.isDark
                ? context.bgCard.withValues(alpha: 0.8)
                : const Color(0xFFF1F5FB),
          ],
        ),
        boxShadow: AppShadows.card(context.isDark),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'TREN KETINGGIAN AIR',
            style: GoogleFonts.inter(
              fontSize: 8.5,
              fontWeight: FontWeight.w800,
              color: AppColors.accent.withValues(alpha: 0.8),
              letterSpacing: 1.2,
            ),
          ),
          const SizedBox(height: 16),
          SizedBox(
            height: 140,
            child: Obx(() {
              if (controller.sparklineData.isEmpty) {
                return Center(
                  child: Text('Menunggu data...',
                      style: TextStyle(color: context.textMuted, fontSize: 12)),
                );
              }
              final data = controller.sparklineData;
              final spots = <FlSpot>[];
              for (int i = 0; i < data.length; i++) {
                // Aligment agar data masuk dari kanan (seperti web)
                // Titik terbaru selalu di x = 39
                double x = (39 - (data.length - 1 - i)).toDouble();
                spots.add(FlSpot(x, data[i]));
              }

              double minY =
                  spots.map((e) => e.y).reduce((a, b) => a < b ? a : b) - 0.05;
              double maxY =
                  spots.map((e) => e.y).reduce((a, b) => a > b ? a : b) + 0.05;
              if (minY == maxY) {
                minY -= 0.1;
                maxY += 0.1;
              }

              return LineChart(
                duration: Duration.zero, // Real-time feel seperti web
                LineChartData(
                  lineTouchData: LineTouchData(
                    getTouchedSpotIndicator:
                        (LineChartBarData barData, List<int> spotIndexes) {
                      return spotIndexes.map((index) {
                        return TouchedSpotIndicatorData(
                          FlLine(
                            color: AppColors.accent.withValues(alpha: 0.5),
                            strokeWidth: 1,
                            dashArray: [5, 5],
                          ),
                          FlDotData(
                            show: true,
                            getDotPainter: (spot, percent, barData, index) =>
                                FlDotCirclePainter(
                              radius: 3,
                              color: AppColors.accent,
                              strokeWidth: 1.5,
                              strokeColor: context.bgCard,
                            ),
                          ),
                        );
                      }).toList();
                    },
                    touchTooltipData: LineTouchTooltipData(
                      getTooltipColor: (spot) => context.bgCard,
                      tooltipBorderRadius: BorderRadius.circular(12),
                      getTooltipItems: (touchedSpots) {
                        return touchedSpots.map((LineBarSpot touchedSpot) {
                          return LineTooltipItem(
                            touchedSpot.y.toStringAsFixed(2),
                            GoogleFonts.rajdhani(
                              color: context.textPrimary,
                              fontWeight: FontWeight.bold,
                              fontSize: 13,
                            ),
                          );
                        }).toList();
                      },
                    ),
                  ),
                  gridData: FlGridData(
                    show: true,
                    drawVerticalLine: false,
                    getDrawingHorizontalLine: (value) => FlLine(
                      color: context.dividerColor.withValues(alpha: 0.2),
                      strokeWidth: 1,
                    ),
                  ),
                  titlesData: FlTitlesData(
                    show: true,
                    topTitles: const AxisTitles(
                        sideTitles: SideTitles(showTitles: false)),
                    rightTitles: const AxisTitles(
                        sideTitles: SideTitles(showTitles: false)),
                    bottomTitles: const AxisTitles(
                        sideTitles: SideTitles(showTitles: false)),
                    leftTitles: AxisTitles(
                      sideTitles: SideTitles(
                        showTitles: true,
                        reservedSize: 35, // Kurangi gap parameter
                        getTitlesWidget: (value, meta) {
                          return Text(
                            value.toStringAsFixed(2),
                            style: GoogleFonts.rajdhani(
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                              color: context.textMuted,
                            ),
                          );
                        },
                      ),
                    ),
                  ),
                  borderData: FlBorderData(
                    show: true,
                    border: Border(
                      left: BorderSide(
                          color: context.dividerColor.withValues(alpha: 0.8),
                          width: 2),
                      bottom: BorderSide(
                          color: context.dividerColor.withValues(alpha: 0.8),
                          width: 2),
                    ),
                  ),
                  minX: 0,
                  maxX: 39,
                  minY: minY,
                  maxY: maxY,
                  lineBarsData: [
                    LineChartBarData(
                      spots: spots,
                      isCurved: true,
                      color: AppColors.accent,
                      barWidth: 3,
                      isStrokeCapRound: true,
                      dotData: const FlDotData(show: false),
                      belowBarData: BarAreaData(
                        show: true,
                        gradient: LinearGradient(
                          begin: Alignment.topCenter,
                          end: Alignment.bottomCenter,
                          colors: [
                            AppColors.accent.withValues(alpha: 0.3),
                            AppColors.accent.withValues(alpha: 0.0),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              );
            }),
          ),
        ],
      ),
    );
  }

  Widget _buildWeatherAndSignalRow(BuildContext context) {
    return Row(
      children: [
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'CUACA LOKASI',
                style: GoogleFonts.inter(
                  fontSize: 9,
                  fontWeight: FontWeight.w800,
                  color: context.textMuted,
                ),
              ),
              const SizedBox(height: 8),
              Obx(() => Row(
                children: [
                  Icon(Icons.air, size: 14, color: AppColors.accent),
                  const SizedBox(width: 4),
                  Text(
                    '${controller.weatherWindspeed.value.toStringAsFixed(1)} km/h',
                    style: GoogleFonts.inter(fontSize: 12, fontWeight: FontWeight.bold, color: context.textPrimary),
                  ),
                  const SizedBox(width: 12),
                  Icon(Icons.water_drop_outlined, size: 14, color: AppColors.accent),
                  const SizedBox(width: 4),
                  Text(
                    '${controller.weatherHumidity.value}%',
                    style: GoogleFonts.inter(fontSize: 12, fontWeight: FontWeight.bold, color: context.textPrimary),
                  ),
                ],
              )),
            ],
          ),
        ),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(
                'SINYAL SENSOR',
                style: GoogleFonts.inter(
                  fontSize: 9,
                  fontWeight: FontWeight.w800,
                  color: context.textMuted,
                ),
              ),
              const SizedBox(height: 8),
              Obx(() {
                final strength = (controller.validCount.value / 20.0).clamp(0.0, 1.0);
                return Row(
                  mainAxisAlignment: MainAxisAlignment.end,
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    for (int i = 0; i < 5; i++)
                      Container(
                        margin: const EdgeInsets.only(left: 4),
                        width: 4,
                        height: 8 + (i * 3).toDouble(),
                        decoration: BoxDecoration(
                          color: (i / 5.0) < strength ? AppColors.statusSafe : context.dividerColor,
                          borderRadius: BorderRadius.circular(2),
                        ),
                      ),
                  ],
                );
              }),
            ],
          ),
        ),
      ],
    );
  }

  // ═══════════════════════════════════════════════════════════════════
  // BOTTOM NAV
  // ═══════════════════════════════════════════════════════════════════

  Widget _buildBottomNav(BuildContext context) {
    return Container(
      height: 90,
      padding: const EdgeInsets.only(bottom: 20),
      decoration: BoxDecoration(
        color: context.bgCard,
        border: Border(top: BorderSide(color: context.borderColor)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          _navItem(context, Icons.dashboard_rounded, 'Dashboard', true),
          _navItem(context, Icons.analytics_rounded, 'Analisis', false,
              onTap: () => Get.toNamed(Routes.ANALYSIS)),
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
          const SizedBox(height: 4),
          Text(
            label,
            style: GoogleFonts.inter(
              fontSize: 10,
              fontWeight: active ? FontWeight.bold : FontWeight.normal,
              color: active ? AppColors.accent : context.textMuted,
            ),
          ),
        ],
      ),
    );
  }
  void _showDevicePicker(BuildContext context) {
    final searchController = TextEditingController();
    final filteredDevices = <Map<String, dynamic>>[].obs;
    filteredDevices.value = controller.devices;

    Get.bottomSheet(
      Container(
        height: MediaQuery.of(context).size.height * 0.7,
        padding: const EdgeInsets.fromLTRB(20, 10, 20, 0),
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
                color: context.dividerColor,
                borderRadius: BorderRadius.circular(2),
              ),
            ),
            const SizedBox(height: 20),
            Text(
              'Pilih Lokasi Pemantauan',
              style: GoogleFonts.inter(
                fontSize: 18,
                fontWeight: FontWeight.w800,
                color: context.textPrimary,
              ),
            ),
            const SizedBox(height: 20),
            // Search Bar (Select2 Style)
            TextField(
              controller: searchController,
              onChanged: (val) {
                filteredDevices.value = controller.devices
                    .where((d) =>
                        (d['name'] ?? '').toLowerCase().contains(val.toLowerCase()) ||
                        (d['location'] ?? '').toLowerCase().contains(val.toLowerCase()))
                    .toList();
              },
              decoration: InputDecoration(
                hintText: 'Cari lokasi atau nama perangkat...',
                prefixIcon: const Icon(Icons.search, color: AppColors.accent),
                filled: true,
                fillColor: context.isDark ? Colors.white.withValues(alpha: 0.05) : Colors.black.withValues(alpha: 0.05),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(16),
                  borderSide: BorderSide.none,
                ),
              ),
            ),
            const SizedBox(height: 20),
            Expanded(
              child: Obx(() => ListView.builder(
                    itemCount: filteredDevices.length,
                    itemBuilder: (context, index) {
                      final device = filteredDevices[index];
                      final isSelected = controller.selectedDeviceSlug.value == device['slug'];
                      
                      return Container(
                        margin: const EdgeInsets.only(bottom: 12),
                        decoration: BoxDecoration(
                          color: isSelected ? AppColors.accent.withValues(alpha: 0.1) : Colors.transparent,
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(
                            color: isSelected ? AppColors.accent : context.dividerColor.withValues(alpha: 0.5),
                          ),
                        ),
                        child: ListTile(
                          onTap: () {
                            controller.onDeviceSelected(device);
                            Get.back();
                          },
                          leading: Container(
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(
                              color: isSelected ? AppColors.accent : context.dividerColor,
                              shape: BoxShape.circle,
                            ),
                            child: Icon(
                              Icons.location_on_rounded,
                              size: 20,
                              color: isSelected ? Colors.white : context.textMuted,
                            ),
                          ),
                          title: Text(
                            (device['location'] ?? 'Unknown').toUpperCase(),
                            style: GoogleFonts.inter(
                              fontSize: 14,
                              fontWeight: FontWeight.w800,
                              color: isSelected ? AppColors.accent : context.textPrimary,
                            ),
                          ),
                          subtitle: Text(
                            device['name'] ?? 'Device',
                            style: GoogleFonts.inter(
                              fontSize: 12,
                              color: context.textMuted,
                            ),
                          ),
                          trailing: isSelected 
                            ? const Icon(Icons.check_circle_rounded, color: AppColors.accent)
                            : const Icon(Icons.chevron_right_rounded),
                        ),
                      );
                    },
                  )),
            ),
          ],
        ),
      ),
      isScrollControlled: true,
    );
  }
}
