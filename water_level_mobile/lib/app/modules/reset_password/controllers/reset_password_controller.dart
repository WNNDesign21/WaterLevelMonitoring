import 'package:get/get.dart';

class ResetPasswordController extends GetxController {
  final isPasswordVisible = false.obs;
  final isConfirmPasswordVisible = false.obs;
  
  // Mock account name
  final accountName = "Ahmad Syarifuddin".obs;

  void togglePasswordVisibility() => isPasswordVisible.value = !isPasswordVisible.value;
  void toggleConfirmPasswordVisibility() => isConfirmPasswordVisible.value = !isConfirmPasswordVisible.value;

  void updatePassword() {
    // Logic to update password
    Get.snackbar('Sukses', 'Kata sandi Anda telah berhasil diperbarui.');
    Get.offAllNamed('/login');
  }
}
