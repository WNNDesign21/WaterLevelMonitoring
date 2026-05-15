import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:geolocator/geolocator.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:water_level_mobile/app/data/providers/api_provider.dart';
import 'package:water_level_mobile/app/routes/app_pages.dart';
import '../../settings/controllers/settings_controller.dart';
import '../../home/controllers/home_controller.dart';

class EditProfileController extends GetxController {
  final ApiProvider apiProvider = ApiProvider();
  final GetStorage storage = GetStorage();
  
  final isLoading = false.obs;
  final MapController mapController = MapController();

  final nameController = TextEditingController();
  final whatsappController = TextEditingController();
  final addressController = TextEditingController();
  final emergencyContactController = TextEditingController();

  final latitude = (-6.3012).obs;
  final longitude = (107.3054).obs;

  @override
  void onInit() {
    super.onInit();
    _loadCurrentData();
  }

  void _loadCurrentData() {
    final user = storage.read('user');
    if (user != null) {
      nameController.text = user['name'] ?? '';
      whatsappController.text = user['phone'] ?? '';
      addressController.text = user['address'] ?? '';
      emergencyContactController.text = user['emergency_phone'] ?? '';
      
      if (user['latitude'] != null) {
        latitude.value = double.tryParse(user['latitude'].toString()) ?? -6.3012;
      }
      if (user['longitude'] != null) {
        longitude.value = double.tryParse(user['longitude'].toString()) ?? 107.3054;
      }
    }
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

  Future<void> onUpdateProfile() async {
    if (nameController.text.isEmpty || 
        whatsappController.text.isEmpty || 
        addressController.text.isEmpty || 
        emergencyContactController.text.isEmpty) {
      Get.snackbar('Error', 'Semua field harus diisi', backgroundColor: Colors.red, colorText: Colors.white);
      return;
    }

    isLoading.value = true;
    try {
      final token = storage.read('token');
      final data = {
        'name': nameController.text.trim(),
        'phone': whatsappController.text.trim(),
        'address': addressController.text.trim(),
        'latitude': latitude.value,
        'longitude': longitude.value,
        'emergency_phone': emergencyContactController.text.trim(),
      };

      final response = await apiProvider.updateProfile(data, token);
      
      if (response['statusCode'] == 200) {
        // Update stored user data
        storage.write('user', response['data']['user']);
        
        // Refresh controllers to reflect changes
        if (Get.isRegistered<SettingsController>()) {
          Get.find<SettingsController>().onInit();
        }
        if (Get.isRegistered<HomeController>()) {
          Get.find<HomeController>().onInit();
        }
        
        Get.back();
        Get.snackbar('Berhasil', 'Profil Anda telah diperbarui!', backgroundColor: Colors.green, colorText: Colors.white);
      } else {
        Get.snackbar('Gagal', response['data']['message'] ?? 'Gagal memperbarui profil', backgroundColor: Colors.red, colorText: Colors.white);
      }
    } catch (e) {
      Get.snackbar('Error', 'Terjadi kesalahan koneksi', backgroundColor: Colors.red, colorText: Colors.white);
    } finally {
      isLoading.value = false;
    }
  }

  @override
  void onClose() {
    nameController.dispose();
    whatsappController.dispose();
    addressController.dispose();
    emergencyContactController.dispose();
    super.onClose();
  }
}
