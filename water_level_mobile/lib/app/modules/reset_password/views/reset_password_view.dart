import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../../core/theme/app_theme.dart';
import '../controllers/reset_password_controller.dart';

class ResetPasswordView extends GetView<ResetPasswordController> {
  const ResetPasswordView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: context.bgPrimary,
      appBar: AppBar(
        backgroundColor: context.bgPrimary,
        elevation: 0,
        leading: IconButton(
          icon: Icon(Icons.arrow_back_ios_new_rounded, color: context.textPrimary, size: 18),
          onPressed: () => Get.back(),
        ),
        centerTitle: true,
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 24.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 10),
              
              // Identity Card
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: context.bgCard,
                  borderRadius: BorderRadius.circular(24),
                  border: Border.all(color: context.borderColor),
                  boxShadow: AppShadows.card(context.isDark),
                ),
                child: Row(
                  children: [
                    Container(
                      width: 54,
                      height: 54,
                      decoration: BoxDecoration(
                        color: AppColors.accent.withValues(alpha: 0.1),
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: const Icon(Icons.person_rounded, color: AppColors.accent, size: 26),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Atur ulang sandi untuk:',
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 12,
                              fontWeight: FontWeight.w600,
                              color: context.textMuted,
                            ),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            controller.email,
                            style: GoogleFonts.plusJakartaSans(
                              fontSize: 16,
                              fontWeight: FontWeight.w800,
                              color: context.textPrimary,
                              letterSpacing: -0.3,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              
              const SizedBox(height: 40),
              
              // Title
              Text(
                'Buat Sandi Baru',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 28,
                  fontWeight: FontWeight.w800,
                  color: context.textPrimary,
                  letterSpacing: -0.5,
                ),
              ),
              const SizedBox(height: 12),
              Text(
                'Sandi baru harus berbeda dari sandi yang Anda gunakan sebelumnya.',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 14,
                  fontWeight: FontWeight.w500,
                  color: context.textSecondary,
                  height: 1.6,
                ),
              ),
              
              const SizedBox(height: 40),
              
              // Form
              _buildLabel(context, 'Password Baru'),
              _buildTextField(
                context,
                hint: 'Masukkan password baru',
                icon: Icons.lock_outline_rounded,
                isPassword: true,
                isVisible: controller.isPasswordVisible,
                controller: controller.passwordController,
                onToggle: () => controller.togglePasswordVisibility(),
              ),
              
              const SizedBox(height: 20),
              
              _buildLabel(context, 'Konfirmasi Password'),
              _buildTextField(
                context,
                hint: 'Ulangi password baru',
                icon: Icons.lock_reset_rounded,
                isPassword: true,
                isVisible: controller.isConfirmPasswordVisible,
                controller: controller.confirmPasswordController,
                onToggle: () => controller.toggleConfirmPasswordVisibility(),
              ),
              
              const SizedBox(height: 48),
              
              // Submit Button
              _buildPrimaryButton(
                context,
                text: 'Simpan Kata Sandi Baru',
                onPressed: () => controller.updatePassword(),
              ),
              
              const SizedBox(height: 24),
              
              // Security Note
              Center(
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const Icon(Icons.verified_user_outlined, color: Color(0xFF10B981), size: 16),
                    const SizedBox(width: 8),
                    Text(
                      'Sandi Anda akan dienkripsi dengan aman.',
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: context.textMuted,
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 20),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildLabel(BuildContext context, String text) {
    return Padding(
      padding: const EdgeInsets.only(left: 4, bottom: 8),
      child: Text(
        text,
        style: GoogleFonts.plusJakartaSans(
          fontSize: 13,
          fontWeight: FontWeight.w700,
          color: context.textPrimary,
        ),
      ),
    );
  }

  Widget _buildTextField(
    BuildContext context, {
    required String hint, 
    required IconData icon, 
    bool isPassword = false,
    RxBool? isVisible,
    TextEditingController? controller,
    VoidCallback? onToggle,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: context.bgCard,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: context.borderColor),
      ),
      child: Obx(() => TextField(
        controller: controller,
        obscureText: isPassword && !(isVisible?.value ?? false),
        textAlignVertical: TextAlignVertical.center,
        style: GoogleFonts.plusJakartaSans(
          fontSize: 14,
          fontWeight: FontWeight.w600,
          color: context.textPrimary,
        ),
        decoration: InputDecoration(
          hintText: hint,
          hintStyle: GoogleFonts.plusJakartaSans(
            fontSize: 14, 
            fontWeight: FontWeight.w500,
            color: context.textMuted.withValues(alpha: 0.6),
          ),
          prefixIcon: Icon(icon, size: 20, color: context.textMuted),
          suffixIcon: isPassword ? IconButton(
            icon: Icon(
              (isVisible?.value ?? false) ? Icons.visibility_rounded : Icons.visibility_off_rounded,
              size: 18,
              color: context.textMuted,
            ),
            onPressed: onToggle,
          ) : null,
          border: InputBorder.none,
          contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        ),
      )),
    );
  }

  Widget _buildPrimaryButton(BuildContext context, {required String text, required VoidCallback onPressed}) {
    return SizedBox(
      width: double.infinity,
      height: 60,
      child: Obx(() => ElevatedButton(
        onPressed: controller.isLoading.value ? null : onPressed,
        style: ElevatedButton.styleFrom(
          backgroundColor: AppColors.accent,
          foregroundColor: Colors.white,
          elevation: 0,
          shadowColor: Colors.transparent,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        ),
        child: controller.isLoading.value
            ? const SizedBox(
                height: 20,
                width: 20,
                child: CircularProgressIndicator(
                  strokeWidth: 2,
                  color: Colors.white,
                ),
              )
            : Text(
                text,
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 15,
                  fontWeight: FontWeight.w800,
                  color: Colors.white,
                ),
              ),
      )),
    );
  }
}
