import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:geolocator/geolocator.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:water_level_mobile/app/core/utils/app_snackbar.dart';
import 'package:water_level_mobile/app/data/repositories/auth_repository.dart';
import 'package:water_level_mobile/app/routes/app_pages.dart';

class RegisterController extends GetxController {
  final AuthRepository _authRepo = Get.find<AuthRepository>();
  final GetStorage storage = GetStorage();
  
  final currentStep = 1.obs;
  final pageController = PageController();
  final isLoading = false.obs;
  final isSatelliteMode = false.obs;

  void toggleSatellite() {
    isSatelliteMode.value = !isSatelliteMode.value;
  }

  // Form Fields - Step 1
  final fullNameController = TextEditingController();
  final whatsappController = TextEditingController();
  final emailController = TextEditingController();
  final passwordController = TextEditingController();
  final confirmPasswordController = TextEditingController();

  // Form Fields - Step 2
  final addressController = TextEditingController();
  final emergencyContactController = TextEditingController();

  // Form Fields - Step 3
  final latitude = (-6.3012).obs;
  final longitude = (107.3054).obs;
  final MapController mapController = MapController();

  Future<void> getCurrentLocation() async {
    try {
      bool serviceEnabled;
      LocationPermission permission;

      serviceEnabled = await Geolocator.isLocationServiceEnabled();
      if (!serviceEnabled) return;

      permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
        if (permission == LocationPermission.denied) return;
      }

      if (permission == LocationPermission.deniedForever) return;

      final position = await Geolocator.getCurrentPosition();
      latitude.value = position.latitude;
      longitude.value = position.longitude;

      mapController.move(LatLng(latitude.value, longitude.value), 15);
    } catch (e) {
      print('Error getting location: $e');
    }
  }

  void updateLocation(LatLng position) {
    latitude.value = position.latitude;
    longitude.value = position.longitude;
  }

  void nextStep() {
    if (currentStep.value < 3) {
      if (currentStep.value == 1) {
        if (!_validateStep1()) return;
      } else if (currentStep.value == 2) {
        if (!_validateStep2()) return;
      }

      currentStep.value++;
      pageController.nextPage(
        duration: const Duration(milliseconds: 300),
        curve: Curves.easeInOut,
      );
    } else {
      onRegister();
    }
  }

  bool _validateStep1() {
    if (fullNameController.text.isEmpty ||
        whatsappController.text.isEmpty ||
        emailController.text.isEmpty ||
        passwordController.text.isEmpty ||
        confirmPasswordController.text.isEmpty) {
      AppSnackbar.show(
        title: 'Form Belum Lengkap',
        message: 'Harap isi semua kolom informasi akun.',
        isError: true,
      );
      return false;
    }
    if (passwordController.text != confirmPasswordController.text) {
      AppSnackbar.show(
        title: 'Password Tidak Cocok',
        message: 'Konfirmasi password harus sama dengan password.',
        isError: true,
      );
      return false;
    }
    return true;
  }

  bool _validateStep2() {
    if (addressController.text.isEmpty || emergencyContactController.text.isEmpty) {
      AppSnackbar.show(
        title: 'Form Belum Lengkap',
        message: 'Harap isi alamat dan kontak darurat Anda.',
        isError: true,
      );
      return false;
    }
    return true;
  }

  Future<void> onRegister() async {
    isLoading.value = true;
    try {
      final userData = {
        'name': fullNameController.text.trim(),
        'email': emailController.text.trim(),
        'password': passwordController.text,
        'password_confirmation': confirmPasswordController.text,
        'phone': whatsappController.text.trim(),
        'address': addressController.text.trim(),
        'latitude': latitude.value,
        'longitude': longitude.value,
        'emergency_phone': emergencyContactController.text.trim(),
      };

      final response = await _authRepo.register(userData);
      
      if (response != null && (response['statusCode'] == 200 || response['statusCode'] == 201)) {
        Get.offAllNamed(Routes.HOME);
        AppSnackbar.show(
          title: 'Berhasil',
          message: 'Akun Anda berhasil dibuat. Selamat datang!',
        );
      } else if (response != null) {
        String msg = response['data']['message'] ?? 'Gagal mendaftar';
        if (response['data']['errors'] != null) {
          msg = (response['data']['errors'] as Map).values.first[0];
        }
        AppSnackbar.show(
          title: 'Registrasi Gagal',
          message: msg,
          isError: true,
        );
      }
    } catch (e) {
      AppSnackbar.show(
        title: 'Kesalahan Koneksi',
        message: 'Terjadi kesalahan saat mendaftar. Silakan cek internet Anda.',
        isError: true,
      );
    } finally {
      isLoading.value = false;
    }
  }

  void previousStep() {
    if (currentStep.value > 1) {
      currentStep.value--;
      pageController.previousPage(
        duration: const Duration(milliseconds: 300),
        curve: Curves.easeInOut,
      );
    } else {
      Get.back();
    }
  }

  @override
  void onClose() {
    pageController.dispose();
    fullNameController.dispose();
    whatsappController.dispose();
    emailController.dispose();
    passwordController.dispose();
    confirmPasswordController.dispose();
    addressController.dispose();
    emergencyContactController.dispose();
    super.onClose();
  }
}
