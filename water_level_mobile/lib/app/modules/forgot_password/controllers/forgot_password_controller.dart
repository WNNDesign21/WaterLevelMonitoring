import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:water_level_mobile/app/data/repositories/auth_repository.dart';
import 'package:water_level_mobile/app/core/utils/app_snackbar.dart';

class ForgotPasswordController extends GetxController {
  final AuthRepository _authRepo = Get.find<AuthRepository>();
  final emailController = TextEditingController();
  final isLoading = false.obs;

  Future<void> sendToEmail() async {
    await _sendResetLink('email');
  }

  Future<void> sendToWhatsApp() async {
    await _sendResetLink('whatsapp');
  }

  Future<void> _sendResetLink(String method) async {
    final email = emailController.text.trim();
    if (email.isEmpty) {
      AppSnackbar.show(title: 'Error', message: 'Silakan masukkan email Anda', isError: true);
      return;
    }

    isLoading.value = true;
    try {
      final response = await _authRepo.forgotPassword(email, method);
      if (response['statusCode'] == 200) {
        AppSnackbar.show(title: 'Sukses', message: response['data']['message']);
      } else {
        AppSnackbar.show(title: 'Gagal', message: response['data']['message'] ?? 'Gagal mengirim link reset', isError: true);
      }
    } catch (e) {
      AppSnackbar.show(title: 'Error', message: 'Terjadi kesalahan koneksi', isError: true);
    } finally {
      isLoading.value = false;
    }
  }

  @override
  void onClose() {
    emailController.dispose();
    super.onClose();
  }
}
