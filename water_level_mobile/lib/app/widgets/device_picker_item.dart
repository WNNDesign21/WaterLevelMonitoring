import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import '../modules/home/controllers/home_controller.dart';
import '../core/theme/app_theme.dart';
import 'dart:ui' as ui;
import 'package:water_level_mobile/app/data/models/device_model.dart';

class DevicePickerItem extends StatelessWidget {
  final DeviceModel device;
  final bool isSelected;
  final VoidCallback onTap;

  const DevicePickerItem({
    super.key,
    required this.device,
    required this.isSelected,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final homeController = Get.find<HomeController>();
    final slug = device.slug;

    return Obx(() {
      final isDefault = homeController.defaultDeviceSlug.value == slug;

      return Container(
        margin: const EdgeInsets.only(bottom: 12),
        decoration: BoxDecoration(
          color: isSelected
              ? AppColors.accent.withValues(alpha: 0.08)
              : context.bgCard,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(
            color: isSelected
                ? AppColors.accent
                : context.dividerColor.withValues(alpha: 0.2),
            width: isSelected ? 1.5 : 1,
          ),
          boxShadow: isSelected
              ? [
                  BoxShadow(
                    color: AppColors.accent.withValues(alpha: 0.1),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  )
                ]
              : null,
        ),
        child: Material(
          color: Colors.transparent,
          child: InkWell(
            onTap: onTap,
            borderRadius: BorderRadius.circular(16),
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
              child: Row(
                children: [
                  // Leading Icon
                  Container(
                    width: 44,
                    height: 44,
                    decoration: BoxDecoration(
                      color: isSelected
                          ? AppColors.accent
                          : context.dividerColor.withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Icon(
                      Icons.sensors_rounded,
                      size: 22,
                      color: isSelected ? Colors.white : context.textMuted,
                    ),
                  ),
                  const SizedBox(width: 16),
                  
                  // Text Info
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          (device.location ?? 'Unknown').toUpperCase(),
                          style: GoogleFonts.rajdhani(
                            fontSize: 15,
                            fontWeight: FontWeight.w800,
                            color: isSelected ? AppColors.accent : context.textPrimary,
                            letterSpacing: 0.5,
                            height: 1.1,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          device.name,
                          style: GoogleFonts.inter(
                            fontSize: 11,
                            fontWeight: FontWeight.w500,
                            color: context.textMuted,
                          ),
                        ),
                        
                        // Small Indicator if this is the current session choice but NOT default
                        if (isSelected && !isDefault)
                          Padding(
                            padding: const EdgeInsets.only(top: 4),
                            child: Text(
                              'SEDANG DIPANTAU',
                              style: GoogleFonts.inter(
                                fontSize: 8,
                                fontWeight: FontWeight.w800,
                                color: AppColors.accent,
                                letterSpacing: 0.5,
                              ),
                            ),
                          ),
                      ],
                    ),
                  ),
                  
                  // Instagram Style Pin Icon (Trailing)
                  Material(
                    color: Colors.transparent,
                    child: InkWell(
                      onTap: () => homeController.toggleDefault(device),
                      borderRadius: BorderRadius.circular(100),
                      child: Container(
                        padding: const EdgeInsets.all(8),
                        child: AnimatedSwitcher(
                          duration: const Duration(milliseconds: 300),
                          transitionBuilder: (Widget child, Animation<double> animation) {
                            return ScaleTransition(scale: animation, child: child);
                          },
                          child: Icon(
                            isDefault ? Icons.push_pin_rounded : Icons.push_pin_outlined,
                            key: ValueKey(isDefault),
                            color: isDefault ? AppColors.accent : context.textMuted.withValues(alpha: 0.3),
                            size: 24,
                          ),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      );
    });
  }
}

class DevicePickerSmartOptions extends StatelessWidget {
  final HomeController controller;
  final BuildContext context;
  final bool isDefaultMode;

  const DevicePickerSmartOptions({
    super.key,
    required this.controller,
    required this.context,
    this.isDefaultMode = false,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Expanded(
          child: _buildSmartSelectChip(
            context,
            label: 'Sesuai Profil',
            icon: Icons.person_pin_circle_rounded,
            onTap: () {
              controller.autoSelectByProfile(isDefaultMode: isDefaultMode);
            },
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: _buildSmartSelectChip(
            context,
            label: 'Gunakan GPS',
            icon: Icons.gps_fixed_rounded,
            onTap: () {
              controller.autoSelectByGPS(isDefaultMode: isDefaultMode);
            },
          ),
        ),
      ],
    );
  }

  Widget _buildSmartSelectChip(
    BuildContext context, {
    required String label,
    required IconData icon,
    required VoidCallback onTap,
  }) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 12),
          decoration: BoxDecoration(
            color: context.isDark
                ? Colors.white.withValues(alpha: 0.03)
                : Colors.black.withValues(alpha: 0.03),
            borderRadius: BorderRadius.circular(12),
            border: Border.all(
              color: context.dividerColor.withValues(alpha: 0.2),
            ),
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(icon, color: AppColors.accent, size: 14),
              const SizedBox(width: 8),
              Text(
                label.toUpperCase(),
                style: GoogleFonts.inter(
                  fontSize: 10,
                  fontWeight: FontWeight.w800,
                  color: context.textPrimary,
                  letterSpacing: 0.5,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class DevicePickerSearch extends StatelessWidget {
  final TextEditingController controller;
  final ValueChanged<String> onChanged;

  const DevicePickerSearch({
    super.key,
    required this.controller,
    required this.onChanged,
  });

  @override
  Widget build(BuildContext context) {
    return TextField(
      controller: controller,
      onChanged: onChanged,
      style: GoogleFonts.inter(
        fontSize: 13,
        color: context.textPrimary,
      ),
      decoration: InputDecoration(
        hintText: 'Cari lokasi atau perangkat...',
        hintStyle: GoogleFonts.inter(
          fontSize: 11.5,
          fontWeight: FontWeight.w400,
          color: context.textMuted.withValues(alpha: 0.4),
        ),
        prefixIcon: Icon(Icons.search,
            color: AppColors.accent.withValues(alpha: 0.7), size: 20),
        filled: true,
        fillColor: context.isDark
            ? Colors.white.withValues(alpha: 0.05)
            : Colors.black.withValues(alpha: 0.05),
        contentPadding: const EdgeInsets.symmetric(horizontal: 16),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(16),
          borderSide: BorderSide.none,
        ),
      ),
    );
  }
}

class DeviceScanningOverlay extends StatelessWidget {
  final bool isVisible;

  const DeviceScanningOverlay({
    super.key,
    required this.isVisible,
  });

  @override
  Widget build(BuildContext context) {
    if (!isVisible) return const SizedBox.shrink();

    return ClipRRect(
      borderRadius: BorderRadius.circular(16),
      child: BackdropFilter(
        filter: ui.ImageFilter.blur(sigmaX: 8, sigmaY: 8),
        child: Container(
          decoration: BoxDecoration(
            color: context.bgPrimary.withValues(alpha: 0.7),
          ),
          child: Center(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                // Radar Animation
                SizedBox(
                  width: 150,
                  height: 150,
                  child: Stack(
                    alignment: Alignment.center,
                    children: [
                      // Ripple Waves
                      ...List.generate(3, (index) {
                        return RadarPulse(
                          delay: Duration(milliseconds: index * 400),
                        );
                      }),

                      // Scanning Circle
                      Container(
                        width: 120,
                        height: 120,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          border: Border.all(
                            color: AppColors.accent.withValues(alpha: 0.2),
                            width: 1,
                          ),
                        ),
                      ),

                      // Center Icon
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: const BoxDecoration(
                          color: AppColors.accent,
                          shape: BoxShape.circle,
                          boxShadow: [
                            BoxShadow(
                              color: AppColors.accent,
                              blurRadius: 20,
                              spreadRadius: 2,
                            )
                          ],
                        ),
                        child: const Icon(
                          Icons.sensors_rounded,
                          color: Colors.white,
                          size: 32,
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 24),
                Text(
                  'MEMINDAI NODE TERDEKAT...',
                  style: GoogleFonts.rajdhani(
                    fontSize: 14,
                    fontWeight: FontWeight.w800,
                    color: context.textPrimary,
                    letterSpacing: 2.0,
                  ),
                ),
                const SizedBox(height: 8),
                Text(
                  'Menghitung koordinat geografis',
                  style: GoogleFonts.inter(
                    fontSize: 11,
                    fontWeight: FontWeight.w400,
                    color: context.textMuted,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class RadarPulse extends StatefulWidget {
  final Duration delay;
  const RadarPulse({super.key, required this.delay});

  @override
  State<RadarPulse> createState() => _RadarPulseState();
}

class _RadarPulseState extends State<RadarPulse>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _animation;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 2),
    );
    _animation = Tween<double>(begin: 0, end: 1).animate(
      CurvedAnimation(parent: _controller, curve: Curves.easeOut),
    );

    Future.delayed(widget.delay, () {
      if (mounted) _controller.repeat();
    });
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: _animation,
      builder: (context, child) {
        return Container(
          width: 150 * _animation.value,
          height: 150 * _animation.value,
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            border: Border.all(
              color: AppColors.accent.withValues(alpha: 1 - _animation.value),
              width: 2,
            ),
          ),
        );
      },
    );
  }
}
