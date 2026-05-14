import 'package:get/get.dart';

class ForgotPasswordController extends GetxController {
  final emailController = Get.find<GetxController>().obs; // Placeholder if needed

  void sendToEmail() {
    // Logic to send reset link to email
    Get.snackbar('Sukses', 'Instruksi reset sandi telah dikirim ke email Anda.');
  }

  void sendToWhatsApp() {
    // Logic to send reset link to WhatsApp
    Get.snackbar('Sukses', 'Tautan reset sandi telah dikirim ke WhatsApp Anda.');
  }
}
