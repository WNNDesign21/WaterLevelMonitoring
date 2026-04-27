import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';

class SettingsController extends GetxController {
  final _storage = GetStorage();
  final ipController = TextEditingController();

  @override
  void onInit() {
    super.onInit();
    ipController.text = _storage.read('server_ip') ?? '127.0.0.1';
  }

  void saveSettings() {
    if (ipController.text.isNotEmpty) {
      _storage.write('server_ip', ipController.text);
      Get.snackbar(
        'Berhasil',
        'IP Server diperbarui ke ${ipController.text}',
        snackPosition: SnackPosition.BOTTOM,
        backgroundColor: Colors.green,
        colorText: Colors.white,
      );
    }
  }

  @override
  void onClose() {
    ipController.dispose();
    super.onClose();
  }
}
