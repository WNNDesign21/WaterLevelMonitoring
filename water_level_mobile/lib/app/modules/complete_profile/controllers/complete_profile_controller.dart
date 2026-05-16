import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:geolocator/geolocator.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:water_level_mobile/app/data/repositories/auth_repository.dart';
import 'package:water_level_mobile/app/routes/app_pages.dart';
import 'package:water_level_mobile/app/modules/home/controllers/home_controller.dart';
import 'package:water_level_mobile/app/core/utils/app_snackbar.dart';

class CompleteProfileController extends GetxController {
  final AuthRepository _authRepo = Get.find<AuthRepository>();
  final GetStorage storage = GetStorage();
  
  final isLoading = false.obs;
  final isSatelliteMode = false.obs;

  void toggleSatellite() {
    isSatelliteMode.value = !isSatelliteMode.value;
  }
  final MapController mapController = MapController();

  final whatsappController = TextEditingController();
  final addressController = TextEditingController();
  final emergencyContactController = TextEditingController();

  final latitude = (-6.3012).obs;
  final longitude = (107.3054).obs;

  @override
  void onInit() {
    super.onInit();
    getCurrentLocation();
  }

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

  Future<void> onCompleteProfile() async {
    if (whatsappController.text.isEmpty || 
        addressController.text.isEmpty || 
        emergencyContactController.text.isEmpty) {
      AppSnackbar.show(title: 'Error', message: 'Semua field harus diisi', isError: true);
      return;
    }

    isLoading.value = true;
    try {
      final data = {
        'phone': whatsappController.text.trim(),
        'address': addressController.text.trim(),
        'latitude': latitude.value,
        'longitude': longitude.value,
        'emergency_phone': emergencyContactController.text.trim(),
      };
      final response = await _authRepo.completeProfile(data);
      
      if (response != null && response['statusCode'] == 200) {
        // Update stored user data
        storage.write('user', response['data']['user']);
        storage.write('is_guest', false);
        
        Get.find<HomeController>().startMonitoring();
        Get.offAllNamed(Routes.HOME);
        AppSnackbar.show(title: 'Berhasil', message: 'Profil Anda telah dilengkapi!');
      } else {
        AppSnackbar.show(title: 'Gagal', message: response['data']['message'] ?? 'Gagal melengkapi profil', isError: true);
      }
    } catch (e) {
      AppSnackbar.show(title: 'Error', message: 'Terjadi kesalahan sistem', isError: true);
    } finally {
      isLoading.value = false;
    }
  }

  void cancelProfile() {
    storage.erase(); // Hapus data login yang belum lengkap
    Get.offAllNamed(Routes.LOGIN);
  }

  @override
  void onClose() {
    whatsappController.dispose();
    addressController.dispose();
    emergencyContactController.dispose();
    super.onClose();
  }
}
