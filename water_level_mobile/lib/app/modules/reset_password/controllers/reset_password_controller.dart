import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:water_level_mobile/app/data/providers/api_provider.dart';
import 'package:water_level_mobile/app/routes/app_pages.dart';

class ResetPasswordController extends GetxController {
  final ApiProvider apiProvider = ApiProvider();
  
  final passwordController = TextEditingController();
  final confirmPasswordController = TextEditingController();
  
  final isPasswordVisible = false.obs;
  final isConfirmPasswordVisible = false.obs;
  final isLoading = false.obs;

  late String email;
  late String token;

  @override
  void onInit() {
    super.onInit();
    // Get email and token from arguments (passed from deep link or previous page)
    email = Get.arguments?['email'] ?? '';
    token = Get.arguments?['token'] ?? '';
  }

  void togglePasswordVisibility() => isPasswordVisible.value = !isPasswordVisible.value;
  void toggleConfirmPasswordVisibility() => isConfirmPasswordVisible.value = !isConfirmPasswordVisible.value;

  Future<void> updatePassword() async {
    final password = passwordController.text;
    final confirmPassword = confirmPasswordController.text;

    if (password.isEmpty || confirmPassword.isEmpty) {
      Get.snackbar('Error', 'Semua field harus diisi', backgroundColor: Colors.red, colorText: Colors.white);
      return;
    }

    if (password != confirmPassword) {
      Get.snackbar('Error', 'Konfirmasi password tidak cocok', backgroundColor: Colors.red, colorText: Colors.white);
      return;
    }

    isLoading.value = true;
    try {
      final response = await apiProvider.resetPassword({
        'token': token,
        'email': email,
        'password': password,
        'password_confirmation': confirmPassword,
      });

      if (response['statusCode'] == 200) {
        Get.snackbar('Sukses', 'Password berhasil diperbarui!', backgroundColor: Colors.green, colorText: Colors.white);
        Get.offAllNamed(Routes.LOGIN);
      } else {
        Get.snackbar('Gagal', response['data']['message'] ?? 'Gagal mereset password', backgroundColor: Colors.red, colorText: Colors.white);
      }
    } catch (e) {
      Get.snackbar('Error', 'Terjadi kesalahan koneksi', backgroundColor: Colors.red, colorText: Colors.white);
    } finally {
      isLoading.value = false;
    }
  }

  @override
  void onClose() {
    passwordController.dispose();
    confirmPasswordController.dispose();
    super.onClose();
  }
}
