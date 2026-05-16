import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:water_level_mobile/app/core/utils/app_snackbar.dart';
import 'package:water_level_mobile/app/data/repositories/auth_repository.dart';
import 'package:water_level_mobile/app/routes/app_pages.dart';
import '../../home/controllers/home_controller.dart';

class LoginController extends GetxController {
  final AuthRepository _authRepo = Get.find<AuthRepository>();
  final GetStorage storage = GetStorage();
  final GoogleSignIn _googleSignIn = GoogleSignIn();

  final emailController = TextEditingController();
  final passwordController = TextEditingController();
  final isLoading = false.obs;
  final isPasswordVisible = false.obs;
  final rememberMe = false.obs;

  @override
  void onInit() {
    super.onInit();
    emailController.text = storage.read('remember_email') ?? '';
    passwordController.text = storage.read('remember_password') ?? '';
    rememberMe.value = storage.read('remember_me') ?? false;
  }

  void togglePasswordVisibility() {
    isPasswordVisible.value = !isPasswordVisible.value;
  }

  void toggleRememberMe() {
    rememberMe.value = !rememberMe.value;
  }

  Future<void> onLogin() async {
    if (emailController.text.isEmpty || passwordController.text.isEmpty) {
      AppSnackbar.show(
        title: 'Form Belum Lengkap',
        message: 'Harap isi email dan password Anda.',
        isError: true,
      );
      return;
    }

    isLoading.value = true;
    try {
      final user = await _authRepo.login(
        emailController.text.trim(),
        passwordController.text,
      );

      if (user != null) {
        if (rememberMe.value) {
          storage.write('remember_me', true);
          storage.write('remember_email', emailController.text.trim());
          storage.write('remember_password', passwordController.text);
        } else {
          storage.remove('remember_me');
          storage.remove('remember_email');
          storage.remove('remember_password');
        }

        try {
          final homeController = Get.find<HomeController>();
          homeController.stopMonitoring();
          homeController.startMonitoring();
        } catch (_) {}

        Get.offAllNamed(Routes.HOME);
        AppSnackbar.show(
          title: 'Berhasil',
          message: 'Selamat datang kembali, ${user.name}!',
        );
      } else {
        AppSnackbar.show(
          title: 'Login Gagal',
          message: 'Email atau password salah.',
          isError: true,
        );
      }
    } catch (e) {
      AppSnackbar.show(
        title: 'Kesalahan Sistem',
        message: 'Terjadi masalah saat menghubungi server.',
        isError: true,
      );
    } finally {
      isLoading.value = false;
    }
  }

  Future<void> onGoogleLogin() async {
    try {
      // Paksa keluar dulu agar muncul pilihan akun (Account Picker)
      await _googleSignIn.signOut();
      
      final GoogleSignInAccount? googleAccount = await _googleSignIn.signIn();
      if (googleAccount == null) return;

      isLoading.value = true;
      
      final GoogleSignInAuthentication googleAuth = await googleAccount.authentication;

      final response = await _authRepo.googleLogin({
        'name': googleAccount.displayName,
        'email': googleAccount.email,
        'google_id': googleAccount.id,
        'avatar': googleAccount.photoUrl,
        'id_token': googleAuth.idToken,
      });

      if (response != null) {
        final bool isComplete = response['is_complete'] ?? true;
        
        try {
          final homeController = Get.find<HomeController>();
          homeController.stopMonitoring();
          homeController.startMonitoring();
        } catch (_) {}

        if (isComplete) {
          Get.offAllNamed(Routes.HOME);
          AppSnackbar.show(
            title: 'Berhasil',
            message: 'Login Google berhasil. Selamat datang!',
          );
        } else {
          Get.offAllNamed(Routes.COMPLETE_PROFILE);
          AppSnackbar.show(
            title: 'Hampir Selesai',
            message: 'Silakan lengkapi profil Anda terlebih dahulu.',
          );
        }
      } else {
        AppSnackbar.show(
          title: 'Gagal',
          message: 'Gagal masuk menggunakan Google.',
          isError: true,
        );
      }
    } catch (e) {
      AppSnackbar.show(
        title: 'Kesalahan Sistem',
        message: 'Gagal menghubungkan akun Google: $e',
        isError: true,
      );
    } finally {
      isLoading.value = false;
    }
  }

  void onGuestAccess() {
    storage.remove('token');
    storage.remove('user');
    storage.write('is_guest', true);
    
    try {
      final homeController = Get.find<HomeController>();
      homeController.stopMonitoring();
      homeController.startMonitoring();
    } catch (_) {}

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
