import 'package:flutter/material.dart';
import 'package:get/get.dart';

class RegisterController extends GetxController {
  final currentStep = 1.obs;
  final pageController = PageController();

  // Form Fields - Step 1
  final fullName = ''.obs;
  final whatsapp = ''.obs;
  final email = ''.obs;
  final password = ''.obs;
  final confirmPassword = ''.obs;

  // Form Fields - Step 2
  final address = ''.obs;
  final emergencyContact = ''.obs;

  // Form Fields - Step 3
  final latitude = (-6.3012).obs;
  final longitude = (107.3054).obs;

  void nextStep() {
    if (currentStep.value < 3) {
      currentStep.value++;
      pageController.nextPage(
        duration: const Duration(milliseconds: 300),
        curve: Curves.easeInOut,
      );
    } else {
      // Final Registration
      Get.back();
    }
  }

  void previousStep() {
    if (currentStep.value > 1) {
      currentStep.value--;
      pageController.previousPage(
        duration: const Duration(milliseconds: 300),
        curve: Curves.easeInOut,
      );
    } else {
      Get.back();
    }
  }

  @override
  void onClose() {
    pageController.dispose();
    super.onClose();
  }
}
