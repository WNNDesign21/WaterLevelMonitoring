import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';

class AppSnackbar {
  static void show({
    required String title,
    required String message,
    bool isError = false,
  }) {
    Get.snackbar(
      '',
      '',
      titleText: Text(
        title,
        style: GoogleFonts.plusJakartaSans(
          fontSize: 14,
          fontWeight: FontWeight.bold,
          color: Colors.white,
        ),
      ),
      messageText: Text(
        message,
        style: GoogleFonts.plusJakartaSans(
          fontSize: 12,
          color: Colors.white.withOpacity(0.8),
        ),
      ),
      snackPosition: SnackPosition.TOP,
      backgroundColor: const Color(0xFF1E293B).withOpacity(0.9), // Slate 800
      borderRadius: 12,
      margin: const EdgeInsets.all(15),
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 15),
      boxShadows: [
        BoxShadow(
          color: Colors.black.withOpacity(0.2),
          blurRadius: 10,
          offset: const Offset(0, 4),
        ),
      ],
      icon: Icon(
        isError ? Icons.error_outline_rounded : Icons.check_circle_outline_rounded,
        color: isError ? const Color(0xFFFB7185) : const Color(0xFF34D399), // Rose 400 or Emerald 400
        size: 28,
      ),
      borderWidth: 1,
      borderColor: Colors.white.withOpacity(0.1),
      duration: const Duration(seconds: 3),
      overlayBlur: 0.5,
    );
  }
}
