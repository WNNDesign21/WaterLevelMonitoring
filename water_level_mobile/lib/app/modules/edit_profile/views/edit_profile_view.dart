import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import '../../../core/theme/app_theme.dart';
import '../controllers/edit_profile_controller.dart';

class EditProfileView extends GetView<EditProfileController> {
  const EditProfileView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: context.bgPrimary,
      appBar: AppBar(
        backgroundColor: context.bgPrimary,
        elevation: 0,
        leading: IconButton(
          icon: Icon(Icons.arrow_back_ios_new_rounded, color: context.textPrimary, size: 18),
          onPressed: () => Get.back(),
        ),
        title: Text(
          'Edit Profil',
          style: GoogleFonts.plusJakartaSans(
            fontSize: 16,
            fontWeight: FontWeight.w700,
            color: context.textPrimary,
          ),
        ),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        physics: const BouncingScrollPhysics(),
        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            _buildLabel(context, 'Nama Lengkap'),
            _buildTextField(context, hint: 'Masukkan nama...', icon: Icons.person_outline_rounded, controller: controller.nameController),
            const SizedBox(height: 16),

            _buildLabel(context, 'Nomor WhatsApp'),
            _buildTextField(context, hint: '0812xxxx', icon: Icons.phone_android_rounded, keyboardType: TextInputType.phone, controller: controller.whatsappController),
            const SizedBox(height: 16),

            _buildLabel(context, 'Alamat Lengkap'),
            _buildTextField(context, hint: 'Jl. Contoh No. 123...', icon: Icons.home_outlined, maxLines: 3, controller: controller.addressController),
            const SizedBox(height: 16),

            _buildLabel(context, 'Kontak Darurat'),
            _buildTextField(context, hint: '0812xxxx - Nama', icon: Icons.emergency_outlined, controller: controller.emergencyContactController),
            const SizedBox(height: 16),

            _buildLabel(context, 'Lokasi Rumah (Map)'),
            Container(
              height: 250,
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
                            heroTag: 'sat_edit',
                            child: const Icon(Icons.layers_rounded),
                          ),
                          const SizedBox(height: 8),
                          FloatingActionButton.small(
                            onPressed: () => controller.getCurrentLocation(),
                            backgroundColor: context.bgCard,
                            foregroundColor: AppColors.accent,
                            elevation: 2,
                            heroTag: 'loc_edit',
                            child: const Icon(Icons.my_location_rounded),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 32),

            SizedBox(
              width: double.infinity,
              child: Obx(() => ElevatedButton(
                onPressed: controller.isLoading.value ? null : () => controller.onUpdateProfile(),
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.accent,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                  elevation: 0,
                  shadowColor: Colors.transparent,
                  surfaceTintColor: Colors.transparent,
                ),
                child: controller.isLoading.value
                  ? const SizedBox(height: 20, width: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                  : Text(
                      'Simpan Perubahan',
                      style: GoogleFonts.plusJakartaSans(fontSize: 15, fontWeight: FontWeight.w800),
                    ),
              )),
            ),
            const SizedBox(height: 20),
          ],
        ),
      ),
    );
  }

  Widget _buildLabel(BuildContext context, String text) {
    return Padding(
      padding: const EdgeInsets.only(left: 4, bottom: 8),
      child: Text(
        text,
        style: GoogleFonts.plusJakartaSans(
          fontSize: 13,
          fontWeight: FontWeight.w700,
          color: context.textMuted,
        ),
      ),
    );
  }

  Widget _buildTextField(BuildContext context, {
    required String hint, 
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
        keyboardType: keyboardType,
        textAlignVertical: maxLines > 1 ? TextAlignVertical.top : TextAlignVertical.center,
        style: GoogleFonts.plusJakartaSans(fontSize: 14, fontWeight: FontWeight.w600),
        decoration: InputDecoration(
          hintText: hint,
          hintStyle: GoogleFonts.plusJakartaSans(fontSize: 13, color: context.textMuted.withValues(alpha: 0.6), fontWeight: FontWeight.w500),
          border: InputBorder.none,
          prefixIcon: Padding(
            padding: EdgeInsets.only(bottom: maxLines > 1 ? 45 : 0),
            child: Icon(icon, size: 20, color: context.textMuted.withValues(alpha: 0.8)),
          ),
          contentPadding: EdgeInsets.symmetric(horizontal: 16, vertical: maxLines > 1 ? 16 : 14),
        ),
      ),
    );
  }
}
