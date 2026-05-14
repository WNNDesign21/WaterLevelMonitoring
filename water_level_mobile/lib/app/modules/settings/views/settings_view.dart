import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/theme/theme_controller.dart';
import '../controllers/settings_controller.dart';

class SettingsView extends GetView<SettingsController> {
  const SettingsView({super.key});

  @override
  Widget build(BuildContext context) {
    final themeController = Get.find<ThemeController>();

    return Scaffold(
      backgroundColor: context.bgPrimary,
      body: Stack(
        children: [
          // ── Background Decoration ──────────────────────────────────────────
          _buildBackgroundDecoration(context),

          SafeArea(
            child: Column(
              children: [
                _buildAppBar(context),
                Expanded(
                  child: SingleChildScrollView(
                    physics: const BouncingScrollPhysics(),
                    padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // ── Profile Section
                        _buildProfileCard(context),
                        const SizedBox(height: 32),

                        // ── System Configuration
                        _buildSectionHeader(context, 'PENGATURAN SISTEM'),
                        const SizedBox(height: 12),
                        _buildThemeCard(context, themeController),
                        const SizedBox(height: 12),
                        _buildNodeSelectionCard(context),
                        const SizedBox(height: 32),

                        // ── Monitoring Intelligence
                        _buildSectionHeader(context, 'STATUS PERINGATAN'),
                        const SizedBox(height: 12),
                        _buildAlertStatusList(context),
                        const SizedBox(height: 32),

                        // ── Educational Content
                        _buildSectionHeader(context, 'MITIGASI & PENANGGULANGAN'),
                        const SizedBox(height: 12),
                        _buildMitigationCard(context),
                        const SizedBox(height: 32),

                        // ── Corporate Information
                        _buildSectionHeader(context, 'INFORMASI APLIKASI'),
                        const SizedBox(height: 12),
                        _buildInfoCard(context),
                        const SizedBox(height: 12),
                        _buildAboutCard(context),
                        
                        const SizedBox(height: 48),
                        _buildFooter(context),
                        const SizedBox(height: 40),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // ── HELPER WIDGETS ────────────────────────────────────────────────────────

  Widget _buildAppBar(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 8),
      child: Row(
        children: [
          IconButton(
            onPressed: () => Get.back(),
            icon: Icon(Icons.arrow_back_ios_new_rounded,
                color: context.textPrimary, size: 20),
          ),
          const SizedBox(width: 4),
          Text(
            'Pengaturan',
            style: GoogleFonts.plusJakartaSans(
              fontSize: 22,
              fontWeight: FontWeight.w800,
              color: context.textPrimary,
              letterSpacing: -0.5,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildBackgroundDecoration(BuildContext context) {
    return Stack(
      children: [
        Positioned(
          top: -100,
          right: -50,
          child: Container(
            width: 300,
            height: 300,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              gradient: RadialGradient(
                colors: [
                  AppColors.accent.withValues(alpha: context.isDark ? 0.08 : 0.05),
                  Colors.transparent,
                ],
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildProfileCard(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: context.bgCard,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: context.borderColor, width: 1.5),
        boxShadow: AppShadows.card(context.isDark),
      ),
      child: Row(
        children: [
          Container(
            width: 64,
            height: 64,
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [AppColors.accent, AppColors.accent.withValues(alpha: 0.7)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(
                  color: AppColors.accent.withValues(alpha: 0.3),
                  blurRadius: 15,
                  offset: const Offset(0, 5),
                )
              ],
            ),
            child: const Icon(Icons.person_rounded, color: Colors.white, size: 32),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'User Guest',
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 18,
                    fontWeight: FontWeight.w800,
                    color: context.textPrimary,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  'Level: Administrator Akses Terbatas',
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 12,
                    color: context.textMuted,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ],
            ),
          ),
          IconButton(
            onPressed: () {},
            icon: Icon(Icons.edit_note_rounded, color: context.textMuted),
          ),
        ],
      ),
    );
  }

  Widget _buildThemeCard(BuildContext context, ThemeController themeController) {
    return Container(
      decoration: BoxDecoration(
        color: context.bgCard,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: context.borderColor, width: 1.5),
      ),
      child: Obx(() => ListTile(
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
            leading: Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: themeController.isDarkMode 
                  ? Colors.orange.withValues(alpha: 0.1)
                  : Colors.blue.withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(
                themeController.isDarkMode ? Icons.dark_mode_rounded : Icons.light_mode_rounded,
                color: themeController.isDarkMode ? Colors.orange : Colors.blue,
                size: 20,
              ),
            ),
            title: Text(
              'Mode Tampilan',
              style: GoogleFonts.plusJakartaSans(
                fontSize: 14,
                fontWeight: FontWeight.w700,
                color: context.textPrimary,
              ),
            ),
            subtitle: Text(
              themeController.isDarkMode ? 'Tema Gelap Aktif' : 'Tema Terang Aktif',
              style: GoogleFonts.plusJakartaSans(fontSize: 11, color: context.textMuted),
            ),
            trailing: Switch.adaptive(
              value: themeController.isDarkMode,
              onChanged: (_) => themeController.toggleTheme(),
              activeTrackColor: AppColors.accent.withValues(alpha: 0.5),
              activeThumbColor: AppColors.accent,
            ),
          )),
    );
  }

  Widget _buildNodeSelectionCard(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: context.bgCard,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: context.borderColor, width: 1.5),
      ),
      child: Obx(() => ListTile(
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
            onTap: () => _showNodeSelector(context),
            leading: Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: AppColors.accent.withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(10),
              ),
              child: const Icon(Icons.sensors_rounded, color: AppColors.accent, size: 20),
            ),
            title: Text(
              'Node Default',
              style: GoogleFonts.plusJakartaSans(
                fontSize: 14,
                fontWeight: FontWeight.w700,
                color: context.textPrimary,
              ),
            ),
            subtitle: Text(
              controller.homeController.selectedDeviceName.value,
              style: GoogleFonts.plusJakartaSans(fontSize: 11, color: context.textMuted),
            ),
            trailing: Icon(Icons.chevron_right_rounded, color: context.textMuted),
          )),
    );
  }

  Widget _buildAlertStatusList(BuildContext context) {
    return Column(
      children: [
        _buildStatusInfoTile(
          context,
          'AMAN (NORMAL)',
          'Tinggi air di bawah batas normal. Tidak ada potensi banjir.',
          AppColors.statusSafe,
          Icons.check_circle_outline_rounded,
        ),
        const SizedBox(height: 12),
        _buildStatusInfoTile(
          context,
          'WASPADA (SIAGA 3)',
          'Air mulai naik mendekati bantaran sungai. Tingkatkan kewaspadaan.',
          AppColors.statusSiaga3,
          Icons.info_outline_rounded,
        ),
        const SizedBox(height: 12),
        _buildStatusInfoTile(
          context,
          'BAHAYA (SIAGA 1)',
          'Air meluap ke pemukiman. Segera lakukan evakuasi mandiri.',
          AppColors.statusSiaga1,
          Icons.warning_amber_rounded,
        ),
      ],
    );
  }

  Widget _buildStatusInfoTile(BuildContext context, String title, String desc, Color color, IconData icon) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: context.bgCard,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: color.withValues(alpha: 0.3), width: 1.5),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: color, size: 24),
          const SizedBox(width: 14),
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
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  desc,
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 11,
                    color: context.textSecondary,
                    height: 1.4,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMitigationCard(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: context.isDark ? const Color(0xFF1E293B) : Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: context.borderColor, width: 1.5),
        boxShadow: AppShadows.card(context.isDark),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Icon(Icons.medical_services_rounded, color: Colors.redAccent, size: 20),
              const SizedBox(width: 10),
              Text(
                'Panduan Keselamatan',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 14,
                  fontWeight: FontWeight.w800,
                  color: context.textPrimary,
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          _mitigationStep('1', 'Simpan dokumen penting di tempat tinggi/kedap air.'),
          _mitigationStep('2', 'Matikan aliran listrik & cabut peralatan elektronik.'),
          _mitigationStep('3', 'Siapkan tas darurat (P3K, senter, makanan kering).'),
          _mitigationStep('4', 'Ikuti jalur evakuasi menuju titik kumpul terdekat.'),
        ],
      ),
    );
  }

  Widget _mitigationStep(String num, String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('$num.', style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.w800, color: AppColors.accent)),
          const SizedBox(width: 8),
          Expanded(
            child: Text(
              text,
              style: GoogleFonts.plusJakartaSans(fontSize: 12, color: Colors.grey[600], height: 1.4),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoCard(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: context.bgCard,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: context.borderColor, width: 1.5),
      ),
      child: Column(
        children: [
          _buildInfoRow(context, 'Versi Aplikasi', controller.appVersion),
          const Divider(height: 32),
          _buildInfoRow(context, 'Build', controller.appBuild),
          const Divider(height: 32),
          _buildInfoRow(context, 'Developer', controller.developerName),
        ],
      ),
    );
  }

  Widget _buildAboutCard(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: context.isDark 
            ? [const Color(0xFF1E293B), const Color(0xFF0F172A)]
            : [Colors.white, const Color(0xFFF1F5FB)],
        ),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: context.borderColor, width: 1.5),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Tentang Kami',
            style: GoogleFonts.plusJakartaSans(
              fontSize: 14,
              fontWeight: FontWeight.w800,
              color: context.textPrimary,
            ),
          ),
          const SizedBox(height: 12),
          Text(
            'WaterSense adalah platform monitoring ketinggian air berbasis IoT yang dikembangkan untuk memberikan data real-time demi keselamatan masyarakat.',
            style: GoogleFonts.plusJakartaSans(
              fontSize: 12,
              color: context.textSecondary,
              height: 1.5,
            ),
          ),
          const SizedBox(height: 16),
          Text(
            controller.developerWeb,
            style: GoogleFonts.plusJakartaSans(
              fontSize: 12,
              fontWeight: FontWeight.w700,
              color: AppColors.accent,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSectionHeader(BuildContext context, String title) {
    return Padding(
      padding: const EdgeInsets.only(left: 4),
      child: Text(
        title,
        style: GoogleFonts.plusJakartaSans(
          fontSize: 11,
          fontWeight: FontWeight.w900,
          color: context.textMuted,
          letterSpacing: 1.2,
        ),
      ),
    );
  }

  Widget _buildInfoRow(BuildContext context, String label, String value) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 13,
            fontWeight: FontWeight.w600,
            color: context.textSecondary,
          ),
        ),
        Text(
          value,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 13,
            fontWeight: FontWeight.w800,
            color: context.textPrimary,
          ),
        ),
      ],
    );
  }

  Widget _buildFooter(BuildContext context) {
    return Center(
      child: Opacity(
        opacity: 0.5,
        child: Column(
          children: [
            const Icon(Icons.water_drop_rounded, color: AppColors.accent, size: 24),
            const SizedBox(height: 12),
            Text(
              'WATERSENSE CORE v2',
              style: GoogleFonts.plusJakartaSans(
                fontSize: 10,
                fontWeight: FontWeight.w900,
                letterSpacing: 3,
                color: context.textPrimary,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              '© 2026 Cybernova Telemetry Solutions',
              style: GoogleFonts.plusJakartaSans(
                fontSize: 9,
                fontWeight: FontWeight.w600,
                color: context.textMuted,
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _showNodeSelector(BuildContext context) {
    final homeController = controller.homeController;
    
    Get.bottomSheet(
      Container(
        padding: const EdgeInsets.all(24),
        decoration: BoxDecoration(
          color: context.bgPrimary,
          borderRadius: const BorderRadius.vertical(top: Radius.circular(32)),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: context.dividerColor,
                borderRadius: BorderRadius.circular(2),
              ),
            ),
            const SizedBox(height: 24),
            Text(
              'Pilih Node Utama',
              style: GoogleFonts.plusJakartaSans(
                fontSize: 18,
                fontWeight: FontWeight.w800,
                color: context.textPrimary,
              ),
            ),
            const SizedBox(height: 24),
            Flexible(
              child: Obx(() => ListView.separated(
                shrinkWrap: true,
                itemCount: homeController.devices.length,
                separatorBuilder: (_, __) => const SizedBox(height: 12),
                itemBuilder: (context, index) {
                  final device = homeController.devices[index];
                  final isSelected = homeController.selectedDeviceSlug.value == device['slug'];
                  
                  return Container(
                    decoration: BoxDecoration(
                      color: isSelected ? AppColors.accent.withValues(alpha: 0.05) : context.bgCard,
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(
                        color: isSelected ? AppColors.accent : context.borderColor,
                        width: 1.5,
                      ),
                    ),
                    child: ListTile(
                      onTap: () {
                        controller.setDefaultDevice(device);
                        homeController.onDeviceSelected(device);
                        Get.back();
                      },
                      leading: Icon(
                        Icons.sensors_rounded, 
                        color: isSelected ? AppColors.accent : context.textMuted
                      ),
                      title: Text(
                        device['name'] ?? '',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 14,
                          fontWeight: FontWeight.w700,
                          color: isSelected ? AppColors.accent : context.textPrimary,
                        ),
                      ),
                      trailing: isSelected 
                        ? const Icon(Icons.check_circle_rounded, color: AppColors.accent)
                        : null,
                    ),
                  );
                },
              )),
            ),
            const SizedBox(height: 24),
          ],
        ),
      ),
      isScrollControlled: true,
    );
  }
}
