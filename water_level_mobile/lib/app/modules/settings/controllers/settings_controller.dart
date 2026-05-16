import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:water_level_mobile/app/core/utils/app_snackbar.dart';
import 'package:water_level_mobile/app/routes/app_pages.dart';
import 'package:water_level_mobile/app/data/repositories/auth_repository.dart';
import '../../home/controllers/home_controller.dart';

import '../../../data/models/user_model.dart';
import '../../../data/models/device_model.dart';

class SettingsController extends GetxController {
  final _storage = GetStorage();
  final _secureStorage = const FlutterSecureStorage();
  final _authRepo = Get.find<AuthRepository>();
  final homeController = Get.find<HomeController>();

  // Version Info
  final String appVersion = '2.1.0';
  final String appBuild = 'Stable Build 102';
  final String developerName = 'Cybernova Telemetry';
  final String developerWeb = 'http://103.172.205.35/';

  // State
  var isSyncing = false.obs;
  var isGuest = true.obs;
  
  final userName = 'User Guest'.obs;
  final userEmail = 'Administrator Akses Terbatas'.obs;
  final userPhotoUrl = ''.obs;
  var defaultDeviceName = 'Node Terdekat (Auto)'.obs;

  @override
  void onInit() {
    super.onInit();
    _loadUser();
    _updateDefaultDeviceName();
    
    // Dengarkan perubahan storage agar profil langsung terupdate
    _storage.listenKey('user', (value) {
      _loadUser();
    });
  }

  Future<void> _loadUser() async {
    final userData = _storage.read('user');
    final token = await _secureStorage.read(key: 'token');
    
    if (userData != null && token != null) {
      final user = UserModel.fromJson(userData);
      isGuest.value = false;
      userName.value = user.name;
      userEmail.value = user.email;
      userPhotoUrl.value = user.photoUrl ?? '';
    } else {
      isGuest.value = true;
      userName.value = 'User Guest';
      userEmail.value = 'Akses Tamu Terbatas';
      userPhotoUrl.value = '';
    }
  }

  void goToLogin() {
    Get.offAllNamed(Routes.LOGIN);
  }

  void goToRegister() {
    Get.offAllNamed(Routes.LOGIN); // Register is usually linked from Login, but I'll use REGISTER route if available
    // Assuming Routes.REGISTER exists based on conversation context
    // Actually LoginController has onRegister() which goes to Routes.REGISTER
    Get.toNamed(Routes.REGISTER);
  }

  Future<void> launchWebsite() async {
    String webUrl = developerWeb.trim();
    if (!webUrl.startsWith('http://') && !webUrl.startsWith('https://')) {
      webUrl = 'http://$webUrl';
    }
    final Uri url = Uri.parse(webUrl);
    try {
      if (!await launchUrl(url, mode: LaunchMode.externalApplication)) {
        throw 'Could not launch $webUrl';
      }
    } catch (e) {
      AppSnackbar.show(
        title: 'Gagal',
        message: 'Tidak dapat membuka website saat ini.',
        isError: true,
      );
    }
  }

  void logout() async {
    homeController.stopMonitoring();
    await _authRepo.logout();
    Get.offAllNamed(Routes.LOGIN);
    AppSnackbar.show(
      title: 'Sesi Berakhir',
      message: 'Anda telah berhasil keluar dari aplikasi.',
    );
  }

  void onLogout() {
    Get.dialog(
      Dialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        backgroundColor: Colors.transparent,
        child: Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(24),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.red.shade50,
                  shape: BoxShape.circle,
                ),
                child: Icon(Icons.logout_rounded, color: Colors.red.shade600, size: 32),
              ),
              const SizedBox(height: 20),
              Text(
                'Keluar Aplikasi?',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 20,
                  fontWeight: FontWeight.w800,
                  color: const Color(0xFF0F172A),
                ),
              ),
              const SizedBox(height: 12),
              Text(
                'Sesi Anda akan berakhir dan Anda perlu masuk kembali nanti.',
                textAlign: TextAlign.center,
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 14,
                  color: const Color(0xFF64748B),
                  height: 1.5,
                ),
              ),
              const SizedBox(height: 32),
              Row(
                children: [
                  Expanded(
                    child: TextButton(
                      onPressed: () => Get.back(),
                      style: TextButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      child: Text(
                        'Batal',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 14,
                          fontWeight: FontWeight.w700,
                          color: const Color(0xFF64748B),
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: ElevatedButton(
                      onPressed: () => logout(),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.red.shade600,
                        foregroundColor: Colors.white,
                        elevation: 0,
                        shadowColor: Colors.transparent,
                        surfaceTintColor: Colors.transparent,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      child: Text(
                        'Keluar',
                        style: GoogleFonts.plusJakartaSans(
                          fontSize: 14,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  void setDefaultDevice(DeviceModel device) {
    final slug = device.slug;
    _storage.write('default_device_slug', slug);
    homeController.defaultDeviceSlug.value = slug;
    _updateDefaultDeviceName();
    AppSnackbar.show(
      title: 'Perangkat Disetel',
      message: '${device.name} kini menjadi perangkat utama Anda.',
    );
  }

  void _updateDefaultDeviceName() {
    final slug = _storage.read<String>('default_device_slug');
    if (slug != null && homeController.devices.isNotEmpty) {
      final device = homeController.devices.firstWhereOrNull(
        (d) => d.slug == slug,
      );
      if (device != null) {
        defaultDeviceName.value = device.name;
        return;
      }
    }
    defaultDeviceName.value = 'Node Terdekat (Auto)';
  }

  String getDefaultDeviceSlug() {
    return _storage.read('default_device_slug') ?? '';
  }
}
