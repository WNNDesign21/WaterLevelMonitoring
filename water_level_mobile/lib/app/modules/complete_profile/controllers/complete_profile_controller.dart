import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:geolocator/geolocator.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:water_level_mobile/app/data/providers/api_provider.dart';
import 'package:water_level_mobile/app/routes/app_pages.dart';

class CompleteProfileController extends GetxController {
  final ApiProvider apiProvider = ApiProvider();
  final GetStorage storage = GetStorage();
  
  final isLoading = false.obs;
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
      Get.snackbar('Error', 'Semua field harus diisi', backgroundColor: Colors.red, colorText: Colors.white);
      return;
    }

    isLoading.value = true;
    try {
      final token = storage.read('token');
      final data = {
        'phone': whatsappController.text.trim(),
        'address': addressController.text.trim(),
        'latitude': latitude.value,
        'longitude': longitude.value,
        'emergency_phone': emergencyContactController.text.trim(),
      };

      final response = await apiProvider.completeProfile(data, token);
      
      if (response['statusCode'] == 200) {
        // Update stored user data
        storage.write('user', response['data']['user']);
        
        Get.offAllNamed(Routes.HOME);
        Get.snackbar('Berhasil', 'Profil Anda telah dilengkapi!', backgroundColor: Colors.green, colorText: Colors.white);
      } else {
        Get.snackbar('Gagal', response['data']['message'] ?? 'Gagal melengkapi profil', backgroundColor: Colors.red, colorText: Colors.white);
      }
    } catch (e) {
      Get.snackbar('Error', 'Terjadi kesalahan sistem', backgroundColor: Colors.red, colorText: Colors.white);
    } finally {
      isLoading.value = false;
    }
  }

  @override
  void onClose() {
    whatsappController.dispose();
    addressController.dispose();
    emergencyContactController.dispose();
    super.onClose();
  }
}
