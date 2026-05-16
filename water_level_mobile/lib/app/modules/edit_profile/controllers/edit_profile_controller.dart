import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import '../../../data/repositories/auth_repository.dart';
import '../../../data/models/user_model.dart';
import '../../../services/location_service.dart';
import '../../home/controllers/home_controller.dart';
import '../../../core/utils/app_snackbar.dart';

class EditProfileController extends GetxController {
  final AuthRepository _authRepo = Get.find<AuthRepository>();
  final LocationService _locationService = Get.find<LocationService>();
  final GetStorage storage = GetStorage();
  
  final isLoading = false.obs;
  final isSatelliteMode = false.obs;

  void toggleSatellite() {
    isSatelliteMode.value = !isSatelliteMode.value;
  }
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
    final userData = storage.read('user');
    if (userData != null) {
      final user = UserModel.fromJson(userData);
      nameController.text = user.name;
      whatsappController.text = user.phone ?? '';
      addressController.text = user.address ?? '';
      emergencyContactController.text = user.emergencyPhone ?? '';
      
      if (user.latitude != null) latitude.value = user.latitude!;
      if (user.longitude != null) longitude.value = user.longitude!;
    }
  }

  Future<void> getCurrentLocation() async {
    try {
      final position = await _locationService.getCurrentPosition();
      if (position != null) {
        latitude.value = position.latitude;
        longitude.value = position.longitude;
        mapController.move(LatLng(latitude.value, longitude.value), 15);
      }
    } catch (e) {
      debugPrint('Error getting location: $e');
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
      AppSnackbar.show(title: 'Error', message: 'Semua field harus diisi', isError: true);
      return;
    }

    isLoading.value = true;
    try {
      final data = {
        'name': nameController.text.trim(),
        'phone': whatsappController.text.trim(),
        'address': addressController.text.trim(),
        'latitude': latitude.value,
        'longitude': longitude.value,
        'emergency_phone': emergencyContactController.text.trim(),
      };

      final user = await _authRepo.updateProfile(data);
      
      if (user != null) {
        // Refresh controllers to reflect changes
        if (Get.isRegistered<HomeController>()) {
          Get.find<HomeController>().onInit();
        }
        
        Get.back();
        AppSnackbar.show(title: 'Berhasil', message: 'Profil Anda telah diperbarui!');
      } else {
        AppSnackbar.show(title: 'Gagal', message: 'Gagal memperbarui profil', isError: true);
      }
    } catch (e) {
      AppSnackbar.show(title: 'Error', message: 'Terjadi kesalahan koneksi', isError: true);
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
