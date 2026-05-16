import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:water_level_mobile/app/data/repositories/auth_repository.dart';
import 'package:water_level_mobile/app/routes/app_pages.dart';
import 'package:water_level_mobile/app/core/utils/app_snackbar.dart';

class ResetPasswordController extends GetxController {
  final AuthRepository _authRepo = Get.find<AuthRepository>();
  
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
      AppSnackbar.show(title: 'Error', message: 'Semua field harus diisi', isError: true);
      return;
    }

    if (password != confirmPassword) {
      AppSnackbar.show(title: 'Error', message: 'Konfirmasi password tidak cocok', isError: true);
      return;
    }

    isLoading.value = true;
    try {
      final response = await _authRepo.resetPassword({
        'token': token,
        'email': email,
        'password': password,
        'password_confirmation': confirmPassword,
      });

      if (response['statusCode'] == 200) {
        AppSnackbar.show(title: 'Sukses', message: 'Password berhasil diperbarui!');
        Get.offAllNamed(Routes.LOGIN);
      } else {
        AppSnackbar.show(title: 'Gagal', message: response['data']['message'] ?? 'Gagal mereset password', isError: true);
      }
    } catch (e) {
      AppSnackbar.show(title: 'Error', message: 'Terjadi kesalahan koneksi', isError: true);
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
