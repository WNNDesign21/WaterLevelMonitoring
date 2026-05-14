import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/theme/theme_controller.dart';
import '../controllers/device_map_controller.dart';

class DeviceMapView extends GetView<DeviceMapController> {
  const DeviceMapView({super.key});

  @override
  Widget build(BuildContext context) {
    final themeController = Get.find<ThemeController>();
    final isDark = themeController.isDarkMode;

    return Scaffold(
      backgroundColor: context.bgPrimary,
      body: Stack(
        children: [
          // ── Map Layer ──────────────────────────────────────────────────
          Obx(() => FlutterMap(
                mapController: controller.mapController,
                options: MapOptions(
                  initialCenter:
                      const LatLng(-6.5944, 106.7895), // Default to Bogor area
                  initialZoom: 13,
                  onTap: (_, __) => controller.selectedDevice.value = null,
                ),
                children: [
                  TileLayer(
                    urlTemplate: controller.currentMapLayer.value == 1
                        ? 'https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}' // Satellite
                        : controller.currentMapLayer.value == 2
                            ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png' // Dark
                            : 'https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', // Standard
                    subdomains: controller.currentMapLayer.value == 2
                        ? const ['a', 'b', 'c', 'd']
                        : const ['mt0', 'mt1', 'mt2', 'mt3'],
                    userAgentPackageName: 'com.example.water_level_mobile',
                  ),
                  MarkerLayer(
                    markers: controller.filteredDevices.map((device) {
                      final lat = double.tryParse(
                              device['latitude']?.toString() ?? '0') ??
                          0.0;
                      final lng = double.tryParse(
                              device['longitude']?.toString() ?? '0') ??
                          0.0;
                      final isSelected =
                          controller.selectedDevice.value?['slug'] ==
                              device['slug'];

                      final status = device['siaga_status'] ?? 'Aman';
                      final statusColor = status == 'Aman'
                          ? AppColors.statusSafe
                          : (status == 'Waspada'
                              ? AppColors.statusSiaga3
                              : AppColors.statusSiaga1);

                      return Marker(
                        point: LatLng(lat, lng),
                        width: 100,
                        height: 100,
                        child: GestureDetector(
                          onTap: () => controller.selectDevice(device),
                          child: Stack(
                            alignment: Alignment.center,
                            children: [
                              // Pulse Effect for Selected
                              if (isSelected)
                                TweenAnimationBuilder(
                                  tween: Tween(begin: 0.0, end: 1.0),
                                  duration: const Duration(seconds: 2),
                                  builder: (context, double value, child) {
                                    return Container(
                                      width: 40 + (value * 60),
                                      height: 40 + (value * 60),
                                      decoration: BoxDecoration(
                                        shape: BoxShape.circle,
                                        color: statusColor.withValues(
                                            alpha: 1.0 - value),
                                      ),
                                    );
                                  },
                                  onEnd: () {},
                                ),
                              Column(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  AnimatedContainer(
                                    duration: const Duration(milliseconds: 300),
                                    padding: EdgeInsets.all(isSelected ? 6 : 4),
                                    decoration: BoxDecoration(
                                      color: isSelected
                                          ? statusColor
                                          : context.bgCard,
                                      shape: BoxShape.circle,
                                      boxShadow: [
                                        BoxShadow(
                                          color: Colors.black.withValues(
                                              alpha: isSelected ? 0.3 : 0.15),
                                          blurRadius: 10,
                                          offset: const Offset(0, 4),
                                        )
                                      ],
                                      border: Border.all(
                                        color: isSelected
                                            ? Colors.white
                                            : statusColor.withValues(
                                                alpha: 0.8),
                                        width: 2,
                                      ),
                                    ),
                                    child: Icon(
                                      Icons.sensors_rounded,
                                      color: isSelected
                                          ? Colors.white
                                          : statusColor,
                                      size: isSelected ? 22 : 18,
                                    ),
                                  ),
                                  if (isSelected) ...[
                                    const SizedBox(height: 4),
                                    Container(
                                      padding: const EdgeInsets.symmetric(
                                          horizontal: 8, vertical: 3),
                                      decoration: BoxDecoration(
                                        color:
                                            Colors.black.withValues(alpha: 0.8),
                                        borderRadius: BorderRadius.circular(20),
                                      ),
                                      child: Text(
                                        device['name'] ?? '',
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                        style: GoogleFonts.inter(
                                          color: Colors.white,
                                          fontSize: 9,
                                          fontWeight: FontWeight.w800,
                                        ),
                                      ),
                                    ),
                                  ],
                                ],
                              ),
                            ],
                          ),
                        ),
                      );
                    }).toList(),
                  ),
                ],
              )),

          // ── Top Action Bar ─────────────────────────────────────────────
          Positioned(
            top: MediaQuery.of(context).padding.top + 10,
            left: 20,
            right: 20,
            child: Row(
              children: [
                GestureDetector(
                  onTap: () => Get.back(),
                  child: Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: context.bgCard,
                      shape: BoxShape.circle,
                      boxShadow: AppShadows.card(isDark),
                    ),
                    child: Icon(Icons.arrow_back_rounded,
                        color: context.textPrimary),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    height: 48,
                    decoration: BoxDecoration(
                      color: context.bgCard,
                      borderRadius: BorderRadius.circular(16),
                      boxShadow: AppShadows.card(isDark),
                      border: Border.all(color: context.borderColor),
                    ),
                    child: Row(
                      children: [
                        Icon(Icons.search_rounded,
                            color: context.textMuted, size: 18),
                        const SizedBox(width: 12),
                        Expanded(
                          child: TextField(
                            onChanged: (v) => controller.searchQuery.value = v,
                            style: GoogleFonts.inter(
                              fontSize: 13,
                              fontWeight: FontWeight.w600,
                              color: context.textPrimary,
                            ),
                            decoration: InputDecoration(
                              hintText: 'Cari perangkat atau lokasi...',
                              hintStyle: GoogleFonts.inter(
                                fontSize: 13,
                                color: context.textMuted,
                              ),
                              border: InputBorder.none,
                              isDense: true,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),

          // ── Bottom Action & Overlay ─────────────────────────────────────
          Positioned(
            bottom: MediaQuery.of(context).padding.bottom + 20,
            left: 20,
            right: 20,
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                // Floating Actions (Prev/Next + Layer Switcher)
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    // Navigation
                    Obx(() {
                      if (controller.selectedDevice.value == null ||
                          controller.filteredDevices.length <= 1) {
                        return const SizedBox.shrink();
                      }
                      return Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 6, vertical: 6),
                        decoration: BoxDecoration(
                          color: context.bgCard,
                          borderRadius: BorderRadius.circular(30),
                          boxShadow: AppShadows.card(isDark),
                          border: Border.all(color: context.borderColor),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            GestureDetector(
                              onTap: controller.previousDevice,
                              child: Container(
                                padding: const EdgeInsets.all(8),
                                decoration: BoxDecoration(
                                  color: context.bgPrimary,
                                  shape: BoxShape.circle,
                                ),
                                child: Icon(Icons.chevron_left_rounded,
                                    size: 20, color: context.textPrimary),
                              ),
                            ),
                            const SizedBox(width: 12),
                            Text(
                              '${controller.filteredDevices.indexWhere((d) => d['slug'] == controller.selectedDevice.value!['slug']) + 1} / ${controller.filteredDevices.length}',
                              style: GoogleFonts.inter(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w800,
                                  color: context.textSecondary),
                            ),
                            const SizedBox(width: 12),
                            GestureDetector(
                              onTap: controller.nextDevice,
                              child: Container(
                                padding: const EdgeInsets.all(8),
                                decoration: BoxDecoration(
                                  color: context.bgPrimary,
                                  shape: BoxShape.circle,
                                ),
                                child: Icon(Icons.chevron_right_rounded,
                                    size: 20, color: context.textPrimary),
                              ),
                            ),
                          ],
                        ),
                      );
                    }),

                    // Layer Switcher
                    GestureDetector(
                      onTap: () => controller.toggleMapLayer(),
                      child: Obx(() => AnimatedContainer(
                            duration: const Duration(milliseconds: 300),
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: controller.currentMapLayer.value == 1
                                  ? Colors.grey[800]
                                  : context.bgCard,
                              shape: BoxShape.circle,
                              boxShadow: AppShadows.card(isDark),
                              border: Border.all(color: context.borderColor),
                            ),
                            child: Icon(
                              controller.currentMapLayer.value == 1
                                  ? Icons.satellite_alt_rounded
                                  : Icons.map_outlined,
                              color: controller.currentMapLayer.value == 1
                                  ? Colors.white
                                  : context.textPrimary,
                              size: 24,
                            ),
                          )),
                    ),
                  ],
                ),

                // Device Info Card
                Obx(() {
                  final device = controller.selectedDevice.value;
                  if (device == null) return const SizedBox.shrink();

                  final status = device['siaga_status'] ?? 'Aman';
                  final statusColor = status == 'Aman'
                      ? AppColors.statusSafe
                      : (status == 'Waspada'
                          ? AppColors.statusSiaga3
                          : AppColors.statusSiaga1);

                  return Container(
                    margin: const EdgeInsets.only(top: 16),
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: context.bgCard,
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: context.borderColor),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black
                              .withValues(alpha: isDark ? 0.4 : 0.1),
                          blurRadius: 30,
                          offset: const Offset(0, 10),
                        )
                      ],
                    ),
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        // Header Info
                        Row(
                          children: [
                            Container(
                              width: 48,
                              height: 48,
                              decoration: BoxDecoration(
                                color: statusColor.withValues(alpha: 0.1),
                                borderRadius: BorderRadius.circular(12),
                              ),
                              child: Icon(Icons.sensors_rounded,
                                  color: statusColor),
                            ),
                            const SizedBox(width: 16),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    device['name'] ?? 'Node Device',
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                    style: GoogleFonts.inter(
                                      fontSize: 16,
                                      fontWeight: FontWeight.w800,
                                      color: context.textPrimary,
                                      height: 1.2,
                                    ),
                                  ),
                                  Row(
                                    children: [
                                      Icon(Icons.location_on_rounded,
                                          color: context.textMuted, size: 12),
                                      const SizedBox(width: 4),
                                      Expanded(
                                        child: Text(
                                          device['location'] ??
                                              'Unknown Location',
                                          maxLines: 1,
                                          overflow: TextOverflow.ellipsis,
                                          style: GoogleFonts.inter(
                                            fontSize: 11,
                                            color: context.textMuted,
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                ],
                              ),
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 10, vertical: 4),
                              decoration: BoxDecoration(
                                color: statusColor.withValues(alpha: 0.1),
                                borderRadius: BorderRadius.circular(10),
                              ),
                              child: Text(
                                status.toUpperCase(),
                                style: GoogleFonts.inter(
                                  fontSize: 9,
                                  fontWeight: FontWeight.w900,
                                  color: statusColor,
                                ),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 20),
                        // Grid Info
                        Row(
                          children: [
                            Expanded(
                                child: _buildCompactInfo(
                                    context,
                                    Icons.location_on_outlined,
                                    'Latitude',
                                    _formatCoord(device['latitude']))),
                            Container(
                                width: 1,
                                height: 24,
                                color: context.dividerColor
                                    .withValues(alpha: 0.5)),
                            Expanded(
                                child: _buildCompactInfo(
                                    context,
                                    Icons.map_outlined,
                                    'Longitude',
                                    _formatCoord(device['longitude']))),
                            Container(
                                width: 1,
                                height: 24,
                                color: context.dividerColor
                                    .withValues(alpha: 0.5)),
                            Expanded(
                                child: _buildCompactInfo(
                                    context,
                                    Icons.height_rounded,
                                    'Elevation',
                                    '${device['elevation_mdpl'] ?? '0'} m')),
                          ],
                        ),
                        const SizedBox(height: 20),
                        Row(
                          children: [
                            Expanded(
                              child: SizedBox(
                                height: 50,
                                child: ElevatedButton.icon(
                                  onPressed: () => Get.back(),
                                  icon: const Icon(Icons.visibility_rounded,
                                      size: 18),
                                  label: Text(
                                    'DETAIL',
                                    style: GoogleFonts.inter(
                                      fontWeight: FontWeight.w800,
                                      fontSize: 12,
                                      letterSpacing: 0.5,
                                    ),
                                  ),
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: context.bgPrimary,
                                    foregroundColor: context.textPrimary,
                                    elevation: 0,
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(14),
                                      side: BorderSide(
                                          color: context.borderColor),
                                    ),
                                  ),
                                ),
                              ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: SizedBox(
                                height: 50,
                                child: ElevatedButton.icon(
                                  onPressed: () =>
                                      controller.openInGoogleMaps(),
                                  icon: const Icon(Icons.directions_rounded,
                                      size: 18),
                                  label: Text(
                                    'NAVIGASI',
                                    style: GoogleFonts.inter(
                                      fontWeight: FontWeight.w800,
                                      fontSize: 12,
                                      letterSpacing: 0.5,
                                    ),
                                  ),
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: AppColors.accent,
                                    foregroundColor: Colors.white,
                                    elevation: 0,
                                    shape: RoundedRectangleBorder(
                                        borderRadius:
                                            BorderRadius.circular(14)),
                                  ),
                                ),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  );
                }),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCompactInfo(
      BuildContext context, IconData icon, String label, String value) {
    return Column(
      children: [
        Text(
          label.toUpperCase(),
          style: GoogleFonts.inter(
            fontSize: 8,
            fontWeight: FontWeight.w800,
            color: context.textMuted,
            letterSpacing: 0.5,
          ),
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

  String _formatCoord(dynamic value) {
    final double? d = double.tryParse(value?.toString() ?? '');
    if (d == null) return '0.0000';
    return d.toStringAsFixed(4);
  }
}
