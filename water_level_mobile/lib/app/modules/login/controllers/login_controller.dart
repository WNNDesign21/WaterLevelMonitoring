import 'package:get/get.dart';
import 'package:water_level_mobile/app/routes/app_pages.dart';

class LoginController extends GetxController {
  final isPasswordVisible = false.obs;
  final rememberMe = false.obs;

  final emailController = ''.obs;
  final passwordController = ''.obs;

  void togglePasswordVisibility() =>
      isPasswordVisible.value = !isPasswordVisible.value;

  void onLogin() {
    // Mock login
    Get.offAllNamed(Routes.HOME);
  }

  void onGuestAccess() {
    Get.offAllNamed(Routes.HOME);
  }

  void onRegister() {
    Get.toNamed(Routes.REGISTER);
  }
}
