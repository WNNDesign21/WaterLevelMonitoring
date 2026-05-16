import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../../../core/theme/app_theme.dart';
import '../../../routes/app_pages.dart';
import '../../home/controllers/home_controller.dart';
import '../controllers/device_map_controller.dart';

class DeviceMapView extends GetView<DeviceMapController> {
  const DeviceMapView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: context.bgPrimary,
      body: Stack(
        children: [
          // 1. Map Layer (Bottom-most)
          Obx(() => FlutterMap(
                mapController: controller.mapController,
                options: MapOptions(
                  initialCenter: const LatLng(-6.5944, 106.7895),
                  initialZoom: 13,
                  onTap: (_, __) {
                    controller.selectedDevice.value = null;
                    controller.isSearchFocused.value = false;
                    FocusScope.of(context).unfocus();
                  },
                ),
                children: [
                  TileLayer(
                    urlTemplate: controller.currentMapLayer.value == 1
                        ? 'https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}'
                        : controller.currentMapLayer.value == 2
                            ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
                            : 'https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',
                    subdomains: controller.currentMapLayer.value == 2
                        ? const ['a', 'b', 'c', 'd']
                        : const ['mt0', 'mt1', 'mt2', 'mt3'],
                    userAgentPackageName: 'com.example.water_level_mobile',
                  ),
                  MarkerLayer(
                    markers: [
                      // User Location Marker
                      if (controller.gpsLocation.value != null)
                        Marker(
                          point: controller.gpsLocation.value!,
                          width: 40,
                          height: 40,
                          child: Stack(
                            alignment: Alignment.center,
                            children: [
                              Container(
                                width: 20,
                                height: 20,
                                decoration: BoxDecoration(
                                  color: Colors.blue.withValues(alpha: 0.3),
                                  shape: BoxShape.circle,
                                ),
                              ),
                              Container(
                                width: 12,
                                height: 12,
                                decoration: BoxDecoration(
                                  color: Colors.blue,
                                  shape: BoxShape.circle,
                                  border: Border.all(color: Colors.white, width: 2),
                                ),
                              ),
                            ],
                          ),
                        ),
                      // Device Markers
                      ...controller.filteredDevices.map((device) {
                        final lat = device.latitude ?? 0.0;
                        final lng = device.longitude ?? 0.0;
                        final isSelected = controller.selectedDevice.value?.slug == device.slug;
                        final status = device.siagaStatus ?? 'Aman';
                        final statusColor = _getStatusColor(status);

                        return Marker(
                          point: LatLng(lat, lng),
                          width: 120,
                          height: 120,
                          alignment: Alignment.center,
                          child: GestureDetector(
                            onTap: () {
                              controller.selectDevice(device);
                              controller.isSearchFocused.value = false;
                              FocusScope.of(context).unfocus();
                            },
                          child: SizedBox(
                            width: 120,
                            height: 120,
                            child: Stack(
                              clipBehavior: Clip.none,
                              alignment: Alignment.center,
                              children: [
                                // Pulse + Icon Container
                                Center(
                                  child: Stack(
                                    alignment: Alignment.center,
                                    children: [
                                      if (isSelected || status == 'Siaga 1') _PulseAnimation(
                                        color: statusColor,
                                        isCritical: status == 'Siaga 1' && !isSelected,
                                      ),
                                      // Icon Container
                                      AnimatedContainer(
                                        duration: const Duration(milliseconds: 300),
                                        padding: EdgeInsets.all(isSelected ? 6 : 4),
                                        decoration: BoxDecoration(
                                          color: isSelected ? statusColor : context.bgCard,
                                          shape: BoxShape.circle,
                                          boxShadow: isSelected ? [
                                            BoxShadow(
                                              color: statusColor.withValues(alpha: 0.5),
                                              blurRadius: 10,
                                              spreadRadius: 2
                                            )
                                          ] : null,
                                          border: Border.all(
                                            color: isSelected ? Colors.white : statusColor.withValues(alpha: 0.8),
                                            width: 2,
                                          ),
                                        ),
                                        child: Icon(
                                          Icons.sensors_rounded,
                                          color: isSelected ? Colors.white : statusColor,
                                          size: isSelected ? 22 : 18,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                
                                // Weather Context Indicator (NEW)
                                Positioned(
                                  top: 15, // Disesuaikan agar lebih proporsional
                                  right: 15,
                                  child: Obx(() {
                                    final homeController = Get.find<HomeController>();
                                    final globalIcon = homeController.weatherIcon.value;
                                    final isWeatherReady = isSelected && controller.weatherIcon.value.isNotEmpty;
                                    final displayIcon = isWeatherReady ? controller.weatherIcon.value : globalIcon;
                                    
                                    return Container(
                                      padding: const EdgeInsets.all(3),
                                      decoration: BoxDecoration(
                                        color: context.bgCard,
                                        shape: BoxShape.circle,
                                        border: Border.all(
                                          color: isSelected ? statusColor : context.borderColor, 
                                          width: isSelected ? 1.5 : 0.5
                                        ),
                                        boxShadow: isSelected ? [
                                          BoxShadow(color: Colors.black.withValues(alpha: 0.1), blurRadius: 4)
                                        ] : null,
                                      ),
                                      child: displayIcon.isNotEmpty
                                        ? Image.network(
                                            displayIcon,
                                            width: 18,
                                            height: 18,
                                          )
                                        : (isSelected && controller.weatherLoading.value)
                                          ? const SizedBox(
                                              width: 18,
                                              height: 18,
                                              child: Padding(
                                                padding: EdgeInsets.all(2),
                                                child: CircularProgressIndicator(strokeWidth: 1.5),
                                              ),
                                            )
                                          : Icon(
                                              status == 'Aman' ? Icons.wb_sunny_rounded : Icons.umbrella_rounded,
                                              color: status == 'Aman' ? Colors.orange : Colors.blue,
                                              size: 18,
                                            ),
                                    );
                                  }),
                                ),
                                
                                // Device Label
                                Positioned(
                                  bottom: 15,
                                  left: 0,
                                  right: 0,
                                  child: Center(
                                    child: Container(
                                      constraints: const BoxConstraints(maxWidth: 110),
                                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                                      decoration: BoxDecoration(
                                        color: Colors.black.withValues(alpha: 0.8),
                                        borderRadius: BorderRadius.circular(20),
                                        border: isSelected ? Border.all(color: Colors.white, width: 1) : null,
                                      ),
                                      child: Text(
                                        device.name,
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                        textAlign: TextAlign.center,
                                        style: GoogleFonts.inter(
                                          color: Colors.white,
                                          fontSize: 9,
                                          fontWeight: isSelected ? FontWeight.w900 : FontWeight.w700,
                                        ),
                                      ),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                          ),
                        );
                      }),
                    ],
                  ),
                ],
              )),

          // 2. Bottom Action & Overlay (Middle Layer)
          Positioned(
            bottom: MediaQuery.of(context).padding.bottom + 20,
            left: 20,
            right: 20,
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                // Nav & Layer Toggles
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Obx(() {
                      if (controller.filteredDevices.isEmpty) {
                        return const SizedBox.shrink();
                      }
                      
                      final currentIndex = controller.selectedDevice.value == null 
                        ? -1 
                        : controller.filteredDevices.indexWhere((d) => d.slug == controller.selectedDevice.value!.slug);

                      return Container(
                        padding: const EdgeInsets.all(6),
                        decoration: BoxDecoration(
                          color: context.bgCard,
                          borderRadius: BorderRadius.circular(30),
                          border: Border.all(color: context.borderColor),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            GestureDetector(
                              onTap: controller.previousDevice,
                              child: Container(
                                padding: const EdgeInsets.all(8),
                                decoration: BoxDecoration(color: context.bgPrimary, shape: BoxShape.circle),
                                child: Icon(Icons.chevron_left_rounded, size: 20, color: context.textPrimary),
                              ),
                            ),
                            const SizedBox(width: 12),
                            Text(
                              '${currentIndex + 1} / ${controller.filteredDevices.length}',
                              style: GoogleFonts.inter(fontSize: 12, fontWeight: FontWeight.w800, color: context.textSecondary),
                            ),
                            const SizedBox(width: 12),
                            GestureDetector(
                              onTap: controller.nextDevice,
                              child: Container(
                                padding: const EdgeInsets.all(8),
                                decoration: BoxDecoration(color: context.bgPrimary, shape: BoxShape.circle),
                                child: Icon(Icons.chevron_right_rounded, size: 20, color: context.textPrimary),
                              ),
                            ),
                          ],
                        ),
                      );
                    }),
                    GestureDetector(
                      onTap: () => controller.toggleMapLayer(),
                      child: Obx(() => Container(
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: controller.currentMapLayer.value == 1 ? Colors.grey[800] : context.bgCard,
                              shape: BoxShape.circle,
                              border: Border.all(color: context.borderColor),
                            ),
                            child: Icon(
                              controller.currentMapLayer.value == 1 ? Icons.satellite_alt_rounded : Icons.map_outlined,
                              color: controller.currentMapLayer.value == 1 ? Colors.white : context.textPrimary,
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
                  
                  final status = device.siagaStatus ?? 'Aman';
                  final statusColor = _getStatusColor(status);
                  
                  // Handle TMA - fallback to 0.00 if missing
                  final tma = device.waterLevel?.toString() ?? '0.00';
                  
                  // Handle Time
                  String timeStr = '--:--';
                  try {
                    if (device.updatedAt != null) {
                      final dt = DateTime.parse(device.updatedAt.toString()).toLocal();
                      timeStr = DateFormat('HH:mm:ss').format(dt);
                    }
                  } catch (_) {}

                  return Container(
                    margin: const EdgeInsets.only(top: 16),
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      color: context.bgCard,
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: context.borderColor),
                      boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.1), blurRadius: 15, offset: const Offset(0, 5))],
                    ),
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        // Header: Name, Loc, LatLng
                        Row(
                          children: [
                            Container(
                              width: 52,
                              height: 52,
                              decoration: BoxDecoration(color: statusColor.withValues(alpha: 0.1), borderRadius: BorderRadius.circular(14)),
                              child: Icon(Icons.sensors_rounded, color: statusColor, size: 28),
                            ),
                            const SizedBox(width: 16),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    device.name, 
                                    maxLines: 1, 
                                    overflow: TextOverflow.ellipsis, 
                                    style: GoogleFonts.inter(fontSize: 16, fontWeight: FontWeight.w800, color: context.textPrimary, height: 1.2)
                                  ),
                                  const SizedBox(height: 2),
                                  Text(
                                    device.location ?? 'Unknown Location', 
                                    maxLines: 1, 
                                    overflow: TextOverflow.ellipsis, 
                                    style: GoogleFonts.inter(fontSize: 11, color: context.textMuted)
                                  ),
                                  const SizedBox(height: 4),
                                  // Latitude & Longitude moved here
                                  Row(
                                    children: [
                                      Icon(Icons.my_location_rounded, size: 10, color: context.textMuted.withValues(alpha: 0.6)),
                                      const SizedBox(width: 4),
                                      Text(
                                        'LAT: ${_formatCoord(device.latitude)} | LNG: ${_formatCoord(device.longitude)}',
                                        style: GoogleFonts.rajdhani(
                                          fontSize: 10,
                                          fontWeight: FontWeight.w700,
                                          color: context.textMuted.withValues(alpha: 0.8),
                                          letterSpacing: 0.5,
                                        ),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 6),
                                  // NEW: Distance Row
                                  Obx(() {
                                    final gpsDist = controller.getGpsDistance(device);
                                    final profileDist = controller.getProfileDistance(device);
                                    
                                    if (gpsDist == null && profileDist == null) return const SizedBox.shrink();
                                    
                                    return Wrap(
                                      spacing: 8,
                                      runSpacing: 4,
                                      children: [
                                        if (gpsDist != null)
                                          _buildDistanceChip(context, Icons.gps_fixed_rounded, 'GPS', gpsDist),
                                        if (profileDist != null)
                                          _buildDistanceChip(context, Icons.home_rounded, 'Profile', profileDist),
                                      ],
                                    );
                                  }),
                                ],
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 20),
                        
                        // Stats Row: Status | TMA | Waktu
                        Container(
                          padding: const EdgeInsets.symmetric(vertical: 12),
                          decoration: BoxDecoration(
                            color: context.bgPrimary.withValues(alpha: 0.5),
                            borderRadius: BorderRadius.circular(14),
                            border: Border.all(color: context.borderColor.withValues(alpha: 0.5)),
                          ),
                          child: Row(
                            children: [
                              // Status Column
                              Expanded(
                                child: Column(
                                  children: [
                                    Text('STATUS', style: _labelStyle(context)),
                                    const SizedBox(height: 4),
                                    Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                      decoration: BoxDecoration(
                                        color: statusColor.withValues(alpha: 0.15),
                                        borderRadius: BorderRadius.circular(6),
                                      ),
                                      child: Text(
                                        status.toUpperCase(),
                                        style: GoogleFonts.inter(
                                          fontSize: 10,
                                          fontWeight: FontWeight.w900,
                                          color: statusColor,
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              _divider(context),
                              // TMA Column
                              Expanded(
                                child: Column(
                                  children: [
                                    Text('TMA', style: _labelStyle(context)),
                                    const SizedBox(height: 4),
                                    Text(
                                      '$tma m',
                                      style: GoogleFonts.rajdhani(
                                        fontSize: 18,
                                        fontWeight: FontWeight.w800,
                                        color: context.textPrimary,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              _divider(context),
                              // Waktu Column
                              Expanded(
                                child: Column(
                                  children: [
                                    Text('WAKTU', style: _labelStyle(context)),
                                    const SizedBox(height: 4),
                                    Text(
                                      timeStr,
                                      style: GoogleFonts.rajdhani(
                                        fontSize: 16,
                                        fontWeight: FontWeight.w700,
                                        color: context.textPrimary,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ),

                        // Weather Row (REFINED)
                        const SizedBox(height: 12),
                        Obx(() {
                          if (controller.weatherIcon.value.isEmpty) return const SizedBox.shrink();
                          return Container(
                            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                            decoration: BoxDecoration(
                              color: context.bgPrimary.withValues(alpha: 0.3),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Row(
                              children: [
                                Image.network(
                                  controller.weatherIcon.value,
                                  width: 32,
                                  height: 32,
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        controller.weatherDesc.value,
                                        style: GoogleFonts.inter(fontSize: 11, fontWeight: FontWeight.w800, color: context.textPrimary),
                                      ),
                                      const SizedBox(height: 4),
                                      // All metrics in one line
                                      Row(
                                        children: [
                                          _buildWeatherTag(context, Icons.thermostat_rounded, '${controller.weatherTemp.value.toStringAsFixed(1)}°C'),
                                          const SizedBox(width: 8),
                                          _buildWeatherTag(context, Icons.air_rounded, '${controller.weatherWindspeed.value.toStringAsFixed(1)}km/h'),
                                          const SizedBox(width: 8),
                                          _buildWeatherTag(context, Icons.water_drop_outlined, '${controller.weatherHumidity.value}%'),
                                        ],
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          );
                        }),
                        const SizedBox(height: 20),
                        
                        // Action Buttons
                        Row(
                          children: [
                            Expanded(
                              child: ElevatedButton.icon(
                                onPressed: () {
                                  // Select the device in HomeController so Analysis module loads it
                                  final homeController = Get.find<HomeController>();
                                  homeController.onDeviceSelected(device);
                                  Get.toNamed(Routes.ANALYSIS);
                                },
                                icon: const Icon(Icons.analytics_rounded, size: 18),
                                label: const Text('ANALISIS'),
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: context.bgPrimary,
                                  foregroundColor: context.textPrimary,
                                  elevation: 0,
                                  shadowColor: Colors.transparent,
                                  padding: const EdgeInsets.symmetric(vertical: 14),
                                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14), side: BorderSide(color: context.borderColor)),
                                ),
                              ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: ElevatedButton.icon(
                                onPressed: () => controller.openInGoogleMaps(),
                                icon: const Icon(Icons.directions_rounded, size: 18),
                                label: const Text('NAVIGASI'),
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: AppColors.accent,
                                  foregroundColor: Colors.white,
                                  elevation: 0,
                                  shadowColor: Colors.transparent,
                                  padding: const EdgeInsets.symmetric(vertical: 14),
                                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
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

          // 3. Top Action Bar & Search Results (Top-most Layer)
          Positioned(
            top: MediaQuery.of(context).padding.top + 10,
            left: 20,
            right: 20,
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Row(
                  children: [
                    GestureDetector(
                      onTap: () {
                        controller.isSearchFocused.value = false;
                        FocusScope.of(context).unfocus();
                        Get.back();
                      },
                      child: Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(color: context.bgCard, shape: BoxShape.circle, border: Border.all(color: context.borderColor)),
                        child: Icon(Icons.arrow_back_rounded, color: context.textPrimary),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 16),
                        height: 48,
                        decoration: BoxDecoration(color: context.bgCard, borderRadius: BorderRadius.circular(16), border: Border.all(color: context.borderColor)),
                        child: Row(
                          children: [
                            Icon(Icons.search_rounded, color: context.textMuted, size: 18),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Focus(
                                onFocusChange: (hasFocus) => controller.isSearchFocused.value = hasFocus,
                                child: TextField(
                                  onChanged: (v) => controller.searchQuery.value = v,
                                  onTap: () => controller.isSearchFocused.value = true,
                                  style: GoogleFonts.inter(fontSize: 13, fontWeight: FontWeight.w600, color: context.textPrimary),
                                  decoration: InputDecoration(
                                    hintText: 'Cari perangkat...',
                                    hintStyle: GoogleFonts.inter(fontSize: 13, color: context.textMuted),
                                    border: InputBorder.none,
                                    isDense: true,
                                  ),
                                ),
                              ),
                            ),
                            Obx(() => controller.searchQuery.isNotEmpty
                                ? GestureDetector(
                                    onTap: () => controller.searchQuery.value = '',
                                    child: Icon(Icons.close_rounded, color: context.textMuted, size: 18),
                                  )
                                : const SizedBox.shrink()),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
                Obx(() {
                  if (!controller.isSearchFocused.value) return const SizedBox.shrink();
                  final results = controller.filteredDevices;
                  return Container(
                    margin: const EdgeInsets.only(top: 8, left: 60),
                    constraints: BoxConstraints(maxHeight: MediaQuery.of(context).size.height * 0.4),
                    decoration: BoxDecoration(color: context.bgCard, borderRadius: BorderRadius.circular(16), border: Border.all(color: context.borderColor)),
                    child: results.isEmpty
                        ? Padding(padding: const EdgeInsets.all(16.0), child: Text('Tidak ditemukan', style: GoogleFonts.inter(fontSize: 12, color: context.textMuted)))
                        : ListView.separated(
                            shrinkWrap: true,
                            padding: const EdgeInsets.symmetric(vertical: 8),
                            itemCount: results.length,
                            separatorBuilder: (context, index) => Divider(color: context.borderColor, height: 1, indent: 16, endIndent: 16),
                            itemBuilder: (context, index) {
                              final device = results[index];
                              return ListTile(
                                dense: true,
                                leading: Icon(Icons.sensors_rounded, size: 18, color: AppColors.accent),
                                title: Text(device.name, style: GoogleFonts.inter(fontSize: 13, fontWeight: FontWeight.w700, color: context.textPrimary)),
                                subtitle: Text(device.location ?? '', style: GoogleFonts.inter(fontSize: 11, color: context.textMuted)),
                                onTap: () {
                                  controller.selectDevice(device);
                                  controller.isSearchFocused.value = false;
                                  FocusScope.of(context).unfocus();
                                },
                              );
                            },
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

  TextStyle _labelStyle(BuildContext context) => GoogleFonts.inter(fontSize: 8, fontWeight: FontWeight.w800, color: context.textMuted, letterSpacing: 0.5);

  Widget _divider(BuildContext context) => Container(width: 1, height: 24, color: context.dividerColor.withValues(alpha: 0.5));

  Widget _buildDistanceChip(BuildContext context, IconData icon, String label, String value) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
      decoration: BoxDecoration(
        color: AppColors.accent.withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(6),
        border: Border.all(color: AppColors.accent.withValues(alpha: 0.2), width: 0.5),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 8, color: AppColors.accent),
          const SizedBox(width: 4),
          Text(
            '$label: ',
            style: GoogleFonts.inter(fontSize: 8, fontWeight: FontWeight.w500, color: AppColors.accent.withValues(alpha: 0.7)),
          ),
          Text(
            value,
            style: GoogleFonts.inter(fontSize: 8, fontWeight: FontWeight.w900, color: AppColors.accent),
          ),
        ],
      ),
    );
  }

  Widget _buildWeatherTag(BuildContext context, IconData icon, String value) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, size: 10, color: context.textMuted),
        const SizedBox(width: 4),
        Text(
          value,
          style: GoogleFonts.rajdhani(
            fontSize: 11,
            fontWeight: FontWeight.w700,
            color: context.textPrimary,
          ),
        ),
      ],
    );
  }

  Color _getStatusColor(String status) {
    if (status == 'Aman') return AppColors.statusSafe;
    if (status == 'Waspada' || status == 'Siaga 3') return AppColors.statusSiaga3;
    if (status == 'Siaga 2') return AppColors.statusSiaga2;
    if (status == 'Siaga 1') return AppColors.statusSiaga1;
    return Colors.grey; // Fallback for Offline or Unknown
  }

  String _formatCoord(dynamic value) {
    final double? d = double.tryParse(value?.toString() ?? '');
    return d?.toStringAsFixed(4) ?? '0.0000';
  }
}

class _PulseAnimation extends StatefulWidget {
  final Color color;
  final bool isCritical;
  const _PulseAnimation({required this.color, this.isCritical = false});

  @override
  State<_PulseAnimation> createState() => _PulseAnimationState();
}

class _PulseAnimationState extends State<_PulseAnimation> with SingleTickerProviderStateMixin {
  late AnimationController _controller;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: Duration(seconds: widget.isCritical ? 3 : 2),
    )..repeat();
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: _controller,
      builder: (context, child) {
        final double scaleFactor = widget.isCritical ? 30 : 50;
        final double baseSize = widget.isCritical ? 20 : 30;
        
        return Container(
          width: baseSize + (_controller.value * scaleFactor),
          height: baseSize + (_controller.value * scaleFactor),
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            color: widget.color.withValues(alpha: (widget.isCritical ? 0.4 : 1.0) - _controller.value),
          ),
        );
      },
    );
  }
}
