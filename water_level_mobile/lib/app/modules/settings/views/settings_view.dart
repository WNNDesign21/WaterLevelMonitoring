import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:water_level_mobile/app/data/models/device_model.dart';
import 'package:water_level_mobile/app/routes/app_pages.dart';
import '../../../core/theme/app_theme.dart';
import '../../../widgets/device_picker_item.dart';
import '../../../core/theme/theme_controller.dart';
import '../controllers/settings_controller.dart';

class SettingsView extends GetView<SettingsController> {
  const SettingsView({super.key});

  @override
  Widget build(BuildContext context) {
    final themeController = Get.find<ThemeController>();

    return Scaffold(
      backgroundColor: context.bgPrimary,
      body: CustomScrollView(
        physics: const BouncingScrollPhysics(),
        slivers: [
          // ── Premium App Bar ───────────────────────────────────────────
          SliverAppBar(
            expandedHeight: 120,
            floating: false,
            pinned: true,
            backgroundColor: context.bgPrimary,
            elevation: 0,
            leading: IconButton(
              onPressed: () => Get.back(),
              icon: Icon(Icons.arrow_back_ios_new_rounded, color: context.textPrimary, size: 20),
            ),
            flexibleSpace: FlexibleSpaceBar(
              titlePadding: const EdgeInsets.symmetric(horizontal: 56, vertical: 16),
              title: Text(
                'Pengaturan',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 18,
                  fontWeight: FontWeight.w800,
                  color: context.textPrimary,
                  letterSpacing: -0.5,
                ),
              ),
              background: Stack(
                children: [
                  Positioned(
                    top: -40,
                    right: -40,
                    child: Container(
                      width: 180,
                      height: 180,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        gradient: RadialGradient(
                          colors: [
                            AppColors.accent.withValues(alpha: context.isDark ? 0.12 : 0.08),
                            Colors.transparent,
                          ],
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),

          // ── Settings Content ──────────────────────────────────────────
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(20, 0, 20, 40),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // ── Profile Section
                  _buildProfileCard(context),
                  const SizedBox(height: 32),

                  // ── Preferensi Section
                  _buildSectionLabel(context, 'PREFERENSI SISTEM'),
                  _buildSettingsGroup(context, [
                    // Theme Tile (Reactive)
                    Obx(() => _buildSettingsTile(
                      context,
                      icon: themeController.isDarkMode ? Icons.nightlight_round : Icons.wb_sunny_rounded,
                      iconColor: themeController.isDarkMode ? Colors.orange : Colors.blue,
                      title: 'Tema Aplikasi',
                      subtitle: themeController.isDarkMode ? 'Tema Gelap' : 'Tema Terang',
                      trailing: Switch.adaptive(
                        value: themeController.isDarkMode,
                        onChanged: (_) => themeController.toggleTheme(),
                        activeTrackColor: AppColors.accent.withValues(alpha: 0.3),
                        activeThumbColor: AppColors.accent,
                      ),
                    )),
                    // Node Selection Tile (Reactive)
                    Obx(() => _buildSettingsTile(
                      context,
                      icon: Icons.sensors_rounded,
                      iconColor: AppColors.accent,
                      title: 'Node Pemantauan Utama',
                      subtitle: controller.defaultDeviceName.value,
                      onTap: () => _showNodeSelector(context),
                      showChevron: true,
                    )),
                  ]),
                  const SizedBox(height: 32),

                  // ── Legend & Info
                  _buildSectionLabel(context, 'PANDUAN STATUS SIAGA'),
                  _buildStatusLegend(context),
                  const SizedBox(height: 32),

                  // ── Mitigation Section
                  _buildSectionLabel(context, 'MITIGASI DARURAT'),
                  _buildMitigationSection(context),
                  const SizedBox(height: 32),

                  // ── App Info
                  _buildSectionLabel(context, 'INFORMASI APLIKASI'),
                  _buildSettingsGroup(context, [
                    _buildSettingsTile(
                      context,
                      icon: Icons.info_outline_rounded,
                      iconColor: Colors.blue,
                      title: 'Versi Aplikasi',
                      subtitle: '${controller.appVersion} (${controller.appBuild})',
                    ),
                    _buildSettingsTile(
                      context,
                      icon: Icons.code_rounded,
                      iconColor: Colors.green,
                      title: 'Developer',
                      subtitle: controller.developerName,
                    ),
                    _buildSettingsTile(
                      context,
                      icon: Icons.public_rounded,
                      iconColor: Colors.orange,
                      title: 'Website Resmi',
                      subtitle: controller.developerWeb,
                      onTap: () => controller.launchWebsite(),
                      showChevron: true,
                    ),
                    _buildSettingsTile(
                      context,
                      icon: Icons.logout_rounded,
                      iconColor: Colors.red,
                      title: 'Keluar Aplikasi',
                      subtitle: 'Selesaikan sesi Anda saat ini',
                      onTap: () => controller.onLogout(),
                      showChevron: true,
                    ),
                  ]),

                  const SizedBox(height: 48),
                  _buildFooter(context),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSectionLabel(BuildContext context, String label) {
    return Padding(
      padding: const EdgeInsets.only(left: 4, bottom: 12),
      child: Text(
        label,
        style: GoogleFonts.plusJakartaSans(
          fontSize: 10,
          fontWeight: FontWeight.w900,
          color: context.textMuted,
          letterSpacing: 1.5,
        ),
      ),
    );
  }

  Widget _buildProfileCard(BuildContext context) {
    return Obx(() {
      final isGuest = controller.isGuest.value;
      final photoUrl = controller.userPhotoUrl.value;

      return Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: context.bgCard,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: context.borderColor),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: context.isDark ? 0.2 : 0.03),
              blurRadius: 10,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Column(
          children: [
            Row(
              children: [
                // ── Avatar dengan Ring Premium ────────────────────────
                Container(
                  padding: const EdgeInsets.all(3),
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    border: Border.all(
                      color: AppColors.accent.withValues(alpha: 0.2),
                      width: 1.5,
                    ),
                  ),
                  child: Container(
                    width: 64,
                    height: 64,
                    decoration: BoxDecoration(
                      color: context.bgPrimary,
                      shape: BoxShape.circle,
                      image: photoUrl.isNotEmpty
                          ? DecorationImage(image: NetworkImage(photoUrl), fit: BoxFit.cover)
                          : null,
                    ),
                    child: photoUrl.isEmpty ? _buildAvatarFallback(context, isGuest) : null,
                  ),
                ),
                const SizedBox(width: 16),
                
                // ── User Info ──────────────────────────────────────
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Flexible(
                            child: Text(
                              controller.userName.value,
                              style: GoogleFonts.plusJakartaSans(
                                fontSize: 17,
                                fontWeight: FontWeight.w800,
                                color: context.textPrimary,
                                letterSpacing: -0.4,
                              ),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                          if (!isGuest) ...[
                            const SizedBox(width: 6),
                            const Icon(Icons.verified_rounded, size: 16, color: Color(0xFF10B981)),
                          ],
                        ],
                      ),
                      const SizedBox(height: 2),
                      Text(
                        isGuest ? 'Akses Terbatas (Guest Mode)' : controller.userEmail.value,
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 12,
                          fontWeight: FontWeight.w500,
                          color: context.textMuted,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),

                // ── Action (Chevron atau Edit) ─────────────────────
                if (!isGuest)
                  IconButton(
                    onPressed: () => Get.toNamed(Routes.EDIT_PROFILE),
                    icon: Icon(Icons.arrow_forward_ios_rounded, 
                      color: context.textMuted.withValues(alpha: 0.5), 
                      size: 16,
                    ),
                  ),
              ],
            ),

            if (isGuest) ...[
              const SizedBox(height: 16),
              Divider(height: 1, color: context.borderColor.withValues(alpha: 0.5)),
              const SizedBox(height: 16),
              Row(
                children: [
                  Expanded(
                    child: ElevatedButton(
                      onPressed: () => controller.goToLogin(),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppColors.accent,
                        foregroundColor: Colors.white,
                        elevation: 0,
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      child: Text(
                        'Masuk Akun',
                        style: GoogleFonts.plusJakartaSans(fontSize: 13, fontWeight: FontWeight.w700),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () => controller.goToRegister(),
                      style: OutlinedButton.styleFrom(
                        side: BorderSide(color: context.borderColor),
                        foregroundColor: context.textPrimary,
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      child: Text(
                        'Daftar Baru',
                        style: GoogleFonts.plusJakartaSans(fontSize: 13, fontWeight: FontWeight.w700),
                      ),
                    ),
                  ),
                ],
              ),
            ] else ...[
              const SizedBox(height: 16),
              GestureDetector(
                onTap: () => Get.toNamed(Routes.EDIT_PROFILE),
                child: Container(
                  width: double.infinity,
                  padding: const EdgeInsets.symmetric(vertical: 10),
                  decoration: BoxDecoration(
                    color: AppColors.accent.withValues(alpha: 0.08),
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: AppColors.accent.withValues(alpha: 0.1)),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.manage_accounts_rounded, size: 14, color: AppColors.accent),
                      const SizedBox(width: 8),
                      Text(
                        'Kelola Profil & Akun',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 12,
                          fontWeight: FontWeight.w700,
                          color: AppColors.accent,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ],
        ),
      );
    });
  }

  Widget _buildAvatarFallback(BuildContext context, bool isGuest) {
    return Container(
      decoration: BoxDecoration(
        color: AppColors.accent.withValues(alpha: 0.1),
        shape: BoxShape.circle,
      ),
      child: Center(
        child: Icon(
          isGuest ? Icons.person_outline_rounded : Icons.person_rounded,
          color: AppColors.accent.withValues(alpha: 0.6),
          size: 32,
        ),
      ),
    );
  }

  Widget _buildSettingsGroup(BuildContext context, List<Widget> children) {
    return Container(
      decoration: BoxDecoration(
        color: context.bgCard,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: context.borderColor),
      ),
      child: Column(
        children: children,
      ),
    );
  }

  Widget _buildSettingsTile(
    BuildContext context, {
    required IconData icon,
    required Color iconColor,
    required String title,
    required String subtitle,
    Widget? trailing,
    bool showChevron = false,
    VoidCallback? onTap,
  }) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(20),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: iconColor.withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(icon, color: iconColor, size: 20),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 14,
                      fontWeight: FontWeight.w700,
                      color: context.textPrimary,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    subtitle,
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 11,
                      color: context.textMuted,
                    ),
                  ),
                ],
              ),
            ),
            if (trailing != null) trailing,
            if (showChevron && trailing == null)
              Icon(Icons.chevron_right_rounded, color: context.textMuted, size: 20),
          ],
        ),
      ),
    );
  }

  Widget _buildStatusLegend(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 8),
      decoration: BoxDecoration(
        color: context.bgCard,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: context.borderColor),
      ),
      child: Column(
        children: [
          _statusTile(
            context, 
            'NORMAL', 
            'Aman - Ketinggian air normal.', 
            AppColors.statusSafe, 
            Icons.check_circle_rounded
          ),
          _statusTile(
            context, 
            'SIAGA 3', 
            'Waspada - Air mulai naik ke bantaran.', 
            AppColors.statusSiaga3, 
            Icons.warning_amber_rounded
          ),
          _statusTile(
            context, 
            'SIAGA 2', 
            'Siaga Banjir - Genangan masuk pemukiman.', 
            AppColors.statusSiaga2, 
            Icons.notifications_active_rounded
          ),
          _statusTile(
            context, 
            'SIAGA 1', 
            'Evakuasi Kritis! - Bahaya luapan besar.', 
            AppColors.statusSiaga1, 
            Icons.dangerous_rounded
          ),
        ],
      ),
    );
  }

  Widget _statusTile(BuildContext context, String title, String desc, Color color, IconData icon) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: color.withValues(alpha: 0.1),
              shape: BoxShape.circle,
            ),
            child: Icon(icon, color: color, size: 20),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 13, 
                    fontWeight: FontWeight.w800, 
                    color: color,
                    letterSpacing: 0.5
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  desc,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 11, 
                    color: context.textSecondary,
                    height: 1.3
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMitigationSection(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: context.bgCard,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: context.borderColor),
      ),
      child: Column(
        children: [
          _mitigationItem(
            context, 
            Icons.inventory_2_outlined, 
            'Persiapan Awal',
            'Simpan dokumen penting & barang berharga di tempat tinggi atau tas kedap air.'
          ),
          _dividerSmall(context),
          _mitigationItem(
            context, 
            Icons.power_off_outlined, 
            'Keamanan Listrik',
            'Matikan aliran listrik utama dan cabut peralatan elektronik jika air mulai masuk.'
          ),
          _dividerSmall(context),
          _mitigationItem(
            context, 
            Icons.medical_services_outlined, 
            'Tas Siaga Bencana',
            'Siapkan obat-obatan, senter, makanan kering, dan air bersih untuk keadaan darurat.'
          ),
          _dividerSmall(context),
          _mitigationItem(
            context, 
            Icons.map_outlined, 
            'Jalur Evakuasi',
            'Ikuti rute evakuasi resmi menuju titik kumpul atau tempat yang lebih tinggi segera.'
          ),
        ],
      ),
    );
  }

  Widget _mitigationItem(BuildContext context, IconData icon, String title, String desc) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 12),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: context.bgPrimary,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, size: 20, color: AppColors.accent),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 13, 
                    fontWeight: FontWeight.w700, 
                    color: context.textPrimary
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  desc,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 11, 
                    color: context.textSecondary,
                    height: 1.4
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _dividerSmall(BuildContext context) => Divider(height: 1, color: context.borderColor.withValues(alpha: 0.5));

  Widget _buildFooter(BuildContext context) {
    return Center(
      child: Column(
        children: [
          Image.asset(
            context.isDark ? 'assets/images/logo_dark.png' : 'assets/images/logo.png',
            height: 32,
            fit: BoxFit.contain,
            errorBuilder: (context, error, stackTrace) => 
                Icon(Icons.water_drop_rounded, color: AppColors.accent.withValues(alpha: 0.3), size: 24),
          ),
          const SizedBox(height: 12),
          Text(
            'WATERSENSE v${controller.appVersion}',
            style: GoogleFonts.plusJakartaSans(
              fontSize: 10,
              fontWeight: FontWeight.w900,
              letterSpacing: 2,
              color: context.textMuted,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            '© 2026 Cybernova Telemetry Solutions',
            style: GoogleFonts.plusJakartaSans(
              fontSize: 9,
              fontWeight: FontWeight.w600,
              color: context.textMuted.withValues(alpha: 0.5),
            ),
          ),
        ],
      ),
    );
  }

  void _showNodeSelector(BuildContext context) {
    final homeController = controller.homeController;
    final searchController = TextEditingController();
    final filteredDevices = <DeviceModel>[].obs;
    filteredDevices.value = homeController.devices;
    
    Get.bottomSheet(
      Container(
        height: MediaQuery.of(context).size.height * 0.7,
        padding: const EdgeInsets.fromLTRB(24, 12, 24, 0),
        decoration: BoxDecoration(
          color: context.bgPrimary,
          borderRadius: const BorderRadius.vertical(top: Radius.circular(32)),
        ),
        child: Column(
          children: [
            Container(width: 40, height: 4, decoration: BoxDecoration(color: context.dividerColor, borderRadius: BorderRadius.circular(2))),
            const SizedBox(height: 24),
            Text(
              'Pilih Node Utama',
              style: GoogleFonts.plusJakartaSans(fontSize: 18, fontWeight: FontWeight.w800, color: context.textPrimary),
            ),
            const SizedBox(height: 24),
            DevicePickerSmartOptions(
              controller: homeController,
              context: context,
              isDefaultMode: true,
            ),
            const SizedBox(height: 24),
            DevicePickerSearch(
              controller: searchController,
              onChanged: (val) {
                filteredDevices.value = homeController.devices
                    .where((d) =>
                        d.name.toLowerCase().contains(val.toLowerCase()) ||
                        (d.location ?? '')
                            .toLowerCase()
                            .contains(val.toLowerCase()))
                    .toList();
              },
            ),
            const SizedBox(height: 24),
            Expanded(
              child: Stack(
                children: [
                  Obx(() => ListView.separated(
                        physics: const BouncingScrollPhysics(),
                        itemCount: filteredDevices.length,
                        separatorBuilder: (_, __) => const SizedBox(height: 12),
                        itemBuilder: (context, index) {
                          final device = filteredDevices[index];
                          
                          return Obx(() {
                            final isSelected = homeController.defaultDeviceSlug.value == device.slug;
                            
                            return DevicePickerItem(
                              device: device,
                              isSelected: isSelected,
                              onTap: () {
                                // Update selection (as default)
                                controller.setDefaultDevice(device);
                                homeController.onDeviceSelected(device);
                                
                                // Close after a short delay for feedback
                                Future.delayed(const Duration(milliseconds: 300), () {
                                  if (Get.isBottomSheetOpen == true) Get.back();
                                });
                              },
                            );
                          });
                        },
                      )),
                  Obx(() => DeviceScanningOverlay(
                    isVisible: homeController.isSearchingNode.value,
                  )),
                ],
              ),
            ),
          ],
        ),
      ),
      isScrollControlled: true,
    );
  }
}
