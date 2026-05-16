import 'package:get/get.dart';
import '../../data/repositories/weather_repository.dart';
import '../../data/repositories/auth_repository.dart';
import '../../data/repositories/device_repository.dart';
import '../../data/repositories/sensor_repository.dart';
import '../../services/alarm_service.dart';
import '../../services/location_service.dart';


import '../../modules/home/controllers/home_controller.dart';

class InitialBinding extends Bindings {
  @override
  void dependencies() {
    // Core (ThemeController is initialized in main.dart)
    
    // Services
    Get.put(AlarmService(), permanent: true);
    Get.put(LocationService(), permanent: true);
    
    // Repositories
    Get.lazyPut(() => AuthRepository(), fenix: true);
    Get.lazyPut(() => DeviceRepository(), fenix: true);
    Get.lazyPut(() => SensorRepository(), fenix: true);
    Get.lazyPut(() => WeatherRepository(), fenix: true);

    // Controllers
    Get.put(HomeController(), permanent: true);
  }
}
