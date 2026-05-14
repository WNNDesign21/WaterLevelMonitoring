import 'package:get/get.dart';
import '../../../routes/app_pages.dart';

class SplashController extends GetxController {
  @override
  void onInit() {
    super.onInit();
    _startTimer();
  }

  void _startTimer() async {
    await Future.delayed(const Duration(milliseconds: 2500));
    Get.offAllNamed(Routes.LOGIN);
  }
}
