import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:water_level_mobile/app/core/utils/app_snackbar.dart';
import 'package:water_level_mobile/app/data/providers/api_provider.dart';
import 'package:water_level_mobile/app/routes/app_pages.dart';

class LoginController extends GetxController {
  final ApiProvider apiProvider = ApiProvider();
  final GetStorage storage = GetStorage();

  // Instance of GoogleSignIn
  final GoogleSignIn _googleSignIn = GoogleSignIn(
    clientId:
        '449684741383-6unjvhmoumbcj6mlhgjkos2jle986hbp.apps.googleusercontent.com',
  );

  final emailController = TextEditingController();
  final passwordController = TextEditingController();

  final isLoading = false.obs;
  final isPasswordVisible = false.obs;
  final rememberMe = false.obs;

  @override
  void onInit() {
    super.onInit();
    // Load saved email if exists
    final savedEmail = storage.read('saved_email');
    if (savedEmail != null) {
      emailController.text = savedEmail;
      rememberMe.value = true;
    }
  }

  void togglePasswordVisibility() {
    isPasswordVisible.value = !isPasswordVisible.value;
  }

  void toggleRememberMe() {
    rememberMe.value = !rememberMe.value;
  }

  Future<void> onLogin() async {
    final email = emailController.text.trim();
    final password = passwordController.text.trim();

    if (email.isEmpty || password.isEmpty) {
      Get.snackbar('Error', 'Email dan password harus diisi',
          snackPosition: SnackPosition.BOTTOM,
          backgroundColor: Colors.red,
          colorText: Colors.white);
      return;
    }

    isLoading.value = true;
    try {
      final response = await apiProvider.login(email, password);

      if (response['statusCode'] == 200) {
        final data = response['data'];
        final token = data['token'];
        final user = data['user'];

        // Save session
        storage.write('token', token);
        storage.write('user', user);

        if (rememberMe.value) {
          storage.write('saved_email', email);
        } else {
          storage.remove('saved_email');
        }

        Get.offAllNamed(Routes.HOME);
      } else {
        AppSnackbar.show(
          title: 'Login Gagal',
          message: response['data']['message'] ?? 'Terjadi kesalahan pada server.',
          isError: true,
        );
      }
    } catch (e) {
      AppSnackbar.show(
        title: 'Koneksi Bermasalah',
        message: 'Tidak dapat terhubung ke server. Silakan cek koneksi internet Anda.',
        isError: true,
      );
    } finally {
      isLoading.value = false;
    }
  }

  Future<void> onGoogleLogin() async {
    isLoading.value = true;
    try {
      // Paksa logout dari sesi Google sebelumnya agar muncul pilihan akun di Web
      try {
        await _googleSignIn.signOut();
        await _googleSignIn.disconnect();
      } catch (_) {}

      final GoogleSignInAccount? googleUser = await _googleSignIn.signIn();
      if (googleUser == null) {
        isLoading.value = false;
        return;
      }

      final googleData = {
        'email': googleUser.email,
        'name': googleUser.displayName ?? 'User',
        'google_id': googleUser.id,
        'avatar': googleUser.photoUrl,
      };

      // Bersihkan session lama sebelum mulai login baru
      await storage.erase();

      final response = await apiProvider.googleLogin(googleData);

      if (response['statusCode'] == 200) {
        final body = response['data'];
        
        if (body != null) {
          final token = body['token'];
          final user = body['user'];
          final bool needsProfileComplete = body['is_complete'] == false;

          await storage.write('token', token);
          await storage.write('user', user);
          await storage.write('is_logged_in', true);
          await storage.write('is_guest', false);

          if (needsProfileComplete) {
            Get.offAllNamed(Routes.COMPLETE_PROFILE);
          } else {
            Get.offAllNamed(Routes.HOME);
          }
        } else {
          AppSnackbar.show(
            title: 'Oops!',
            message: 'Data login tidak ditemukan. Silakan coba lagi.',
            isError: true,
          );
        }
      } else {
        AppSnackbar.show(
          title: 'Google Login Gagal',
          message: response['data']['message'] ?? 'Gagal masuk melalui Google.',
          isError: true,
        );
      }
    } catch (e) {
      AppSnackbar.show(
        title: 'Kesalahan Sistem',
        message: 'Gagal menghubungkan akun Google Anda. Silakan coba sesaat lagi.',
        isError: true,
      );
    } finally {
      isLoading.value = false;
    }
  }

  void onGuestAccess() {
    storage.remove('token');
    storage.remove('user');
    Get.offAllNamed(Routes.HOME);
  }

  void onRegister() {
    Get.toNamed(Routes.REGISTER);
  }

  @override
  void onClose() {
    emailController.dispose();
    passwordController.dispose();
    super.onClose();
  }
}
