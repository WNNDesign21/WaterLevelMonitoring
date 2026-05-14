import 'package:get/get.dart';
import '../../home/controllers/home_controller.dart';
import '../controllers/analysis_controller.dart';

class AnalysisBinding extends Bindings {
  @override
  void dependencies() {
    if (!Get.isRegistered<HomeController>()) {
      Get.lazyPut<HomeController>(() => HomeController());
    }
    Get.lazyPut<AnalysisController>(
      () => AnalysisController(),
    );
  }
}
