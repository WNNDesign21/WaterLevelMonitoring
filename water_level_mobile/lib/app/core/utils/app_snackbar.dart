import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';

class AppSnackbar {
  static void show({
    required String title,
    required String message,
    bool isError = false,
    bool isWarning = false,
  }) {
    Color getIconColor() {
      if (isError) return const Color(0xFFF87171); // Red
      if (isWarning) return const Color(0xFFFBBF24); // Amber
      return const Color(0xFF34D399); // Emerald
    }

    IconData getIconData() {
      if (isError) return Icons.error_rounded;
      if (isWarning) return Icons.warning_rounded;
      return Icons.check_circle_rounded;
    }

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
      backgroundColor: const Color(0xFF1E293B).withOpacity(0.98),
      borderRadius: 12,
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      boxShadows: [
        BoxShadow(
          color: Colors.black.withOpacity(0.1),
          blurRadius: 8,
          offset: const Offset(0, 2),
        ),
      ],
      icon: Icon(
        getIconData(),
        color: getIconColor(),
        size: 24,
      ),
      duration: const Duration(seconds: 4),
      isDismissible: true,
      dismissDirection: DismissDirection.horizontal,
    );
  }
}
