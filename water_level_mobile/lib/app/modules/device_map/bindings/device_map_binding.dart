import 'package:get/get.dart';
import '../controllers/device_map_controller.dart';

class DeviceMapBinding extends Bindings {
  @override
  void dependencies() {
    Get.lazyPut<DeviceMapController>(
      () => DeviceMapController(),
    );
  }
}
