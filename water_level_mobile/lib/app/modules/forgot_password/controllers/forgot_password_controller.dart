import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:water_level_mobile/app/data/providers/api_provider.dart';

class ForgotPasswordController extends GetxController {
  final ApiProvider apiProvider = ApiProvider();
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
      Get.snackbar('Error', 'Silakan masukkan email Anda', backgroundColor: Colors.red, colorText: Colors.white);
      return;
    }

    isLoading.value = true;
    try {
      final response = await apiProvider.forgotPassword(email, method);
      if (response['statusCode'] == 200) {
        Get.snackbar('Sukses', response['data']['message'], backgroundColor: Colors.green, colorText: Colors.white);
      } else {
        Get.snackbar('Gagal', response['data']['message'] ?? 'Gagal mengirim link reset', backgroundColor: Colors.red, colorText: Colors.white);
      }
    } catch (e) {
      Get.snackbar('Error', 'Terjadi kesalahan koneksi', backgroundColor: Colors.red, colorText: Colors.white);
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
