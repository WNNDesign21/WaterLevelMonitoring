import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart' hide Path;
import '../../../core/theme/app_theme.dart';
import '../controllers/register_controller.dart';

class RegisterView extends GetView<RegisterController> {
  const RegisterView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: context.bgPrimary,
      appBar: AppBar(
        backgroundColor: context.bgPrimary,
        elevation: 0,
        leading: IconButton(
          icon: Icon(Icons.arrow_back_ios_new_rounded, color: context.textPrimary, size: 18),
          onPressed: () => controller.previousStep(),
        ),
        title: Text(
          'Buat Akun Baru',
          style: GoogleFonts.plusJakartaSans(
            fontSize: 16,
            fontWeight: FontWeight.w700,
            color: context.textPrimary,
          ),
        ),
        centerTitle: true,
      ),
      body: Column(
        children: [
          // Step Progress Bar
          _buildStepBar(context),
          
          Expanded(
            child: PageView(
              controller: controller.pageController,
              physics: const NeverScrollableScrollPhysics(),
              children: [
                _buildStep1(context),
                _buildStep2(context),
                _buildStep3(context),
              ],
            ),
          ),
        ],
      ),
      bottomNavigationBar: _buildBottomNav(context),
    );
  }

  Widget _buildStepBar(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 40, vertical: 15),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: List.generate(3, (index) {
              return Obx(() {
                final isCompleted = controller.currentStep.value > index + 1;
                final isActive = controller.currentStep.value == index + 1;
                
                return AnimatedContainer(
                  duration: const Duration(milliseconds: 300),
                  width: 28,
                  height: 28,
                  decoration: BoxDecoration(
                    color: isCompleted || isActive 
                      ? AppColors.accent 
                      : context.borderColor,
                    shape: BoxShape.circle,
                  ),
                  child: Center(
                    child: isCompleted 
                      ? const Icon(Icons.check, size: 14, color: Colors.white)
                      : Text(
                          '${index + 1}',
                          style: GoogleFonts.plusJakartaSans(
                            fontSize: 11,
                            fontWeight: FontWeight.w700,
                            color: isCompleted || isActive ? Colors.white : context.textMuted,
                          ),
                        ),
                  ),
                );
              });
            }).expand((widget) => [
              widget,
              Expanded(
                child: Container(
                  height: 2,
                  margin: const EdgeInsets.symmetric(horizontal: 4),
                  color: context.borderColor,
                ),
              )
            ]).toList()..removeLast(),
          ),
          const SizedBox(height: 10),
          Obx(() => Text(
            _getStepDescription(controller.currentStep.value),
            style: GoogleFonts.plusJakartaSans(
              fontSize: 12,
              fontWeight: FontWeight.w600,
              color: context.textMuted,
            ),
          )),
        ],
      ),
    );
  }

  String _getStepDescription(int step) {
    switch (step) {
      case 1: return 'Informasi Dasar';
      case 2: return 'Domisili & Kontak';
      case 3: return 'Lokasi Rumah';
      default: return '';
    }
  }

  // ── Step 1 ─────────────────────────────────────────────────────────────────
  Widget _buildStep1(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildLabel(context, 'Nama Lengkap'),
          _buildTextField(context, hint: 'Nama lengkap Anda', icon: Icons.person_outline_rounded, controller: controller.fullNameController),
          const SizedBox(height: 16),
          
          _buildLabel(context, 'Nomor WhatsApp'),
          _buildTextField(context, hint: '0812xxxx', icon: Icons.phone_android_rounded, keyboardType: TextInputType.phone, controller: controller.whatsappController),
          const SizedBox(height: 16),
          
          _buildLabel(context, 'Email Aktif'),
          _buildTextField(context, hint: 'email@example.com', icon: Icons.alternate_email_rounded, keyboardType: TextInputType.emailAddress, controller: controller.emailController),
          const SizedBox(height: 16),
          
          _buildLabel(context, 'Kata Sandi'),
          _buildTextField(context, hint: 'Buat kata sandi', isPassword: true, icon: Icons.lock_outline_rounded, controller: controller.passwordController),
          const SizedBox(height: 16),
          
          _buildLabel(context, 'Konfirmasi Sandi'),
          _buildTextField(context, hint: 'Ulangi kata sandi', isPassword: true, icon: Icons.lock_outline_rounded, controller: controller.confirmPasswordController),
          const SizedBox(height: 20),
        ],
      ),
    );
  }

  // ── Step 2 ─────────────────────────────────────────────────────────────────
  Widget _buildStep2(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildLabel(context, 'Alamat Domisili'),
          _buildTextField(context, hint: 'Masukkan alamat lengkap...', icon: Icons.home_work_outlined, maxLines: 3, controller: controller.addressController),
          const SizedBox(height: 24),
          
          _buildLabel(context, 'Kontak Darurat'),
          _buildTextField(context, hint: 'Nomor HP - Nama', icon: Icons.contact_phone_outlined, controller: controller.emergencyContactController),
          
          const SizedBox(height: 30),
          
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: AppColors.accent.withValues(alpha: 0.08),
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: AppColors.accent.withValues(alpha: 0.15)),
            ),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Icon(Icons.info_outline_rounded, color: AppColors.accent, size: 20),
                const SizedBox(width: 12),
                Expanded(
                  child: Text(
                    'Pastikan nomor HP aktif untuk menerima notifikasi peringatan dini.',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      color: AppColors.accent,
                      height: 1.4,
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

  // ── Step 3 ─────────────────────────────────────────────────────────────────
  Widget _buildStep3(BuildContext context) {
    return Column(
      children: [
        Padding(
          padding: const EdgeInsets.fromLTRB(24, 0, 24, 16),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Tentukan Lokasi Rumah',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 14,
                  fontWeight: FontWeight.w700,
                  color: context.textPrimary,
                ),
              ),
            ],
          ),
        ),
        
        Expanded(
          child: Container(
            margin: const EdgeInsets.symmetric(horizontal: 20),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: context.borderColor, width: 1.5),
            ),
            child: ClipRRect(
              borderRadius: BorderRadius.circular(18),
              child: Stack(
                children: [
                  Obx(() => FlutterMap(
                    mapController: controller.mapController,
                    options: MapOptions(
                      initialCenter: LatLng(controller.latitude.value, controller.longitude.value),
                      initialZoom: 15,
                      onTap: (tapPosition, point) => controller.updateLocation(point),
                    ),
                    children: [
                      Obx(() => TileLayer(
                        urlTemplate: controller.isSatelliteMode.value 
                            ? 'https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}'
                            : 'https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',
                        subdomains: const ['mt0', 'mt1', 'mt2', 'mt3'],
                        userAgentPackageName: 'com.watersense.app',
                      )),
                      Obx(() => MarkerLayer(
                        markers: [
                          Marker(
                            point: LatLng(controller.latitude.value, controller.longitude.value),
                            width: 40,
                            height: 40,
                            alignment: Alignment.bottomCenter,
                            child: const Icon(Icons.location_on_rounded, color: Color(0xFFEF4444), size: 40),
                          ),
                        ],
                      )),
                    ],
                  )),
                  Positioned(
                    top: 12,
                    left: 12,
                    right: 12,
                    child: Center(
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                        decoration: BoxDecoration(
                          color: context.bgPrimary.withValues(alpha: 0.9),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Text(
                          'Ketuk peta untuk geser PIN',
                          style: GoogleFonts.plusJakartaSans(
                            color: AppColors.accent,
                            fontSize: 10,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ),
                    ),
                  ),
                  Positioned(
                    right: 12,
                    bottom: 12,
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        FloatingActionButton.small(
                          onPressed: () => controller.toggleSatellite(),
                          backgroundColor: context.bgCard,
                          foregroundColor: AppColors.accent,
                          elevation: 2,
                          heroTag: 'sat_reg',
                          child: const Icon(Icons.layers_rounded),
                        ),
                        const SizedBox(height: 8),
                        FloatingActionButton.small(
                          onPressed: () => controller.getCurrentLocation(),
                          backgroundColor: context.bgCard,
                          foregroundColor: AppColors.accent,
                          elevation: 2,
                          heroTag: 'loc_reg',
                          child: const Icon(Icons.my_location_rounded),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
        
        Padding(
          padding: const EdgeInsets.all(20),
          child: Row(
            children: [
              Expanded(child: Obx(() => _buildLocationCard(context, 'LAT', controller.latitude.value.toString()))),
              const SizedBox(width: 12),
              Expanded(child: Obx(() => _buildLocationCard(context, 'LNG', controller.longitude.value.toString()))),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildLocationCard(BuildContext context, String label, String value) {
    return Container(
      padding: const EdgeInsets.all(10),
      decoration: BoxDecoration(
        color: context.bgCard,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: context.borderColor),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            label,
            style: GoogleFonts.plusJakartaSans(fontSize: 9, fontWeight: FontWeight.w800, color: context.textMuted),
          ),
          const SizedBox(height: 2),
          Text(
            value,
            style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.w700, color: context.textPrimary),
          ),
        ],
      ),
    );
  }

  // ── Common Widgets ────────────────────────────────────────────────────────
  Widget _buildLabel(BuildContext context, String text) {
    return Padding(
      padding: const EdgeInsets.only(left: 4, bottom: 6),
      child: Text(
        text,
        style: GoogleFonts.plusJakartaSans(
          fontSize: 12,
          fontWeight: FontWeight.w700,
          color: context.textMuted,
        ),
      ),
    );
  }

  Widget _buildTextField(BuildContext context, {
    required String hint, 
    bool isPassword = false, 
    required IconData icon, 
    int maxLines = 1,
    TextInputType? keyboardType,
    TextEditingController? controller,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: context.bgCard,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: context.borderColor),
      ),
      child: TextField(
        controller: controller,
        maxLines: maxLines,
        obscureText: isPassword,
        keyboardType: keyboardType,
        textAlignVertical: maxLines > 1 ? TextAlignVertical.top : TextAlignVertical.center,
        style: GoogleFonts.plusJakartaSans(fontSize: 14, fontWeight: FontWeight.w600, color: context.textPrimary),
        decoration: InputDecoration(
          hintText: hint,
          hintStyle: GoogleFonts.plusJakartaSans(fontSize: 13, color: context.textMuted.withValues(alpha: 0.5), fontWeight: FontWeight.w500),
          border: InputBorder.none,
          prefixIcon: Padding(
            padding: EdgeInsets.only(bottom: maxLines > 1 ? 45 : 0),
            child: Icon(icon, size: 18, color: context.textMuted.withValues(alpha: 0.8)),
          ),
          contentPadding: EdgeInsets.symmetric(
            horizontal: 14, 
            vertical: maxLines > 1 ? 16 : 14,
          ),
        ),
      ),
    );
  }

  Widget _buildBottomNav(BuildContext context) {
    return Container(
      padding: const EdgeInsets.fromLTRB(24, 10, 24, 30),
      decoration: BoxDecoration(
        color: context.bgPrimary,
        border: Border(top: BorderSide(color: context.borderColor)),
      ),
      child: Obx(() => Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          if (controller.currentStep.value > 1)
            TextButton.icon(
              onPressed: () => controller.previousStep(),
              icon: const Icon(Icons.arrow_back_ios_new_rounded, size: 12),
              label: Text(
                'Kembali',
                style: GoogleFonts.plusJakartaSans(fontSize: 12, fontWeight: FontWeight.bold),
              ),
              style: TextButton.styleFrom(foregroundColor: context.textMuted, padding: EdgeInsets.zero),
            )
          else
            const SizedBox(),
          
          ElevatedButton(
            onPressed: controller.isLoading.value ? null : () => controller.nextStep(),
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.accent,
              foregroundColor: Colors.white,
              elevation: 0,
              shadowColor: Colors.transparent,
              surfaceTintColor: Colors.transparent,
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 15),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
            child: controller.isLoading.value
                ? const SizedBox(
                    height: 20,
                    width: 20,
                    child: CircularProgressIndicator(
                      strokeWidth: 2,
                      color: Colors.white,
                    ),
                  )
                : Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Text(
                        controller.currentStep.value == 3 ? 'Daftar' : 'Lanjut',
                        style: GoogleFonts.plusJakartaSans(fontSize: 14, fontWeight: FontWeight.w800),
                      ),
                      const SizedBox(width: 8),
                      Icon(
                        controller.currentStep.value == 3 ? Icons.check_circle_rounded : Icons.arrow_forward_ios_rounded,
                        size: 14,
                      ),
                    ],
                  ),
          ),
        ],
      )),
    );
  }
}
