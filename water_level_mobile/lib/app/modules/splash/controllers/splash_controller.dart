import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import '../../../routes/app_pages.dart';

class SplashController extends GetxController {
  final _storage = GetStorage();

  @override
  void onInit() {
    super.onInit();
    _startTimer();
  }

  void _startTimer() async {
    await Future.delayed(const Duration(milliseconds: 2500));
    
    // Check if user is already logged in
    final token = _storage.read('token');
    if (token != null) {
      final user = _storage.read('user');
      final isComplete = user != null && 
                         user['phone'] != null && 
                         user['address'] != null && 
                         user['emergency_phone'] != null;
      
      if (isComplete) {
        Get.offAllNamed(Routes.HOME);
      } else {
        Get.offAllNamed(Routes.COMPLETE_PROFILE);
      }
    } else {
      Get.offAllNamed(Routes.LOGIN);
    }
  }
}
