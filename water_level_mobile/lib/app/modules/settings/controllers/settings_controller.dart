import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import '../../home/controllers/home_controller.dart';

class SettingsController extends GetxController {
  final _storage = GetStorage();
  final homeController = Get.find<HomeController>();

  // Version Info
  final String appVersion = '2.1.0';
  final String appBuild = 'Stable Build 102';
  final String developerName = 'Cybernova Telemetry';
  final String developerWeb = 'www.cybernova.id';

  // State
  var isSyncing = false.obs;

  void setDefaultDevice(Map<String, dynamic> device) {
    final slug = device['slug'] ?? '';
    _storage.write('default_device_slug', slug);
    // Optionally trigger a refresh or notification
    Get.snackbar(
      'Default Node Diperbarui',
      '${device['name']} telah disetel sebagai node utama.',
      snackPosition: SnackPosition.BOTTOM,
      duration: const Duration(seconds: 2),
    );
  }

  String getDefaultDeviceSlug() {
    return _storage.read('default_device_slug') ?? '';
  }
}
