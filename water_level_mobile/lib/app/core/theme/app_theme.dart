import 'package:flutter/material.dart';

// ══════════════════════════════════════════════════════════
// COLOR TOKENS
// ══════════════════════════════════════════════════════════
class AppColors {
  // Dark mode backgrounds
  static const Color darkBgPrimary = Color(0xFF0F1117);
  static const Color darkBgSurface = Color(0xFF1A1D27);
  static const Color darkBgCard = Color(0xFF212435);
  static const Color darkBorder = Color(0xFF2E3147);

  // Light mode backgrounds
  static const Color lightBgPrimary = Color(0xFFF2F4F8);
  static const Color lightBgSurface = Color(0xFFFFFFFF);
  static const Color lightBgCard = Color(0xFFFFFFFF);
  static const Color lightBorder = Color(0xFFE4E8F0);

  // Shared accents
  static const Color accent = Color(0xFF4F7EF8); // Royal blue
  static const Color accentLight = Color(0xFF7BA3FF);
  static const Color accentGlow = Color(0xFF2D5EF0);

  // Status
  static const Color statusSafe = Color(0xFF22C55E);
  static const Color statusSiaga3 = Color(0xFF3B82F6);
  static const Color statusSiaga2 = Color(0xFFF59E0B);
  static const Color statusSiaga1 = Color(0xFFEF4444);

  // Text – dark mode
  static const Color darkTextPrimary = Color(0xFFEAEDF5);
  static const Color darkTextSecondary = Color(0xFF8B91A8);
  static const Color darkTextMuted = Color(0xFF5A607A);

  // Text – light mode
  static const Color lightTextPrimary = Color(0xFF111827);
  static const Color lightTextSecondary = Color(0xFF6B7280);
  static const Color lightTextMuted = Color(0xFF9CA3AF);
}

// ══════════════════════════════════════════════════════════
// THEME DATA
// ══════════════════════════════════════════════════════════
class AppTheme {
  static ThemeData get dark => ThemeData(
        brightness: Brightness.dark,
        scaffoldBackgroundColor: AppColors.darkBgPrimary,
        primaryColor: AppColors.accent,
        colorScheme: ColorScheme.dark(
          primary: AppColors.accent,
          surface: AppColors.darkBgSurface,
          onSurface: AppColors.darkTextPrimary,
        ),
        cardColor: AppColors.darkBgCard,
        dividerColor: AppColors.darkBorder,
        appBarTheme: const AppBarTheme(
          backgroundColor: AppColors.darkBgPrimary,
          elevation: 0,
          surfaceTintColor: Colors.transparent,
          iconTheme: IconThemeData(color: AppColors.darkTextSecondary),
        ),
      );

  static ThemeData get light => ThemeData(
        brightness: Brightness.light,
        scaffoldBackgroundColor: AppColors.lightBgPrimary,
        primaryColor: AppColors.accent,
        colorScheme: ColorScheme.light(
          primary: AppColors.accent,
          surface: AppColors.lightBgSurface,
          onSurface: AppColors.lightTextPrimary,
        ),
        cardColor: AppColors.lightBgCard,
        dividerColor: AppColors.lightBorder,
        appBarTheme: const AppBarTheme(
          backgroundColor: AppColors.lightBgPrimary,
          elevation: 0,
          surfaceTintColor: Colors.transparent,
          iconTheme: IconThemeData(color: AppColors.lightTextSecondary),
        ),
      );
}

// ══════════════════════════════════════════════════════════
// THEME EXTENSIONS — access colors contextually
// ══════════════════════════════════════════════════════════
extension AppThemeX on BuildContext {
  bool get isDark => Theme.of(this).brightness == Brightness.dark;

  Color get bgPrimary =>
      isDark ? AppColors.darkBgPrimary : AppColors.lightBgPrimary;
  Color get bgSurface =>
      isDark ? AppColors.darkBgSurface : AppColors.lightBgSurface;
  Color get bgCard => isDark ? AppColors.darkBgCard : AppColors.lightBgCard;
  Color get borderColor =>
      isDark ? AppColors.darkBorder : AppColors.lightBorder;

  Color get textPrimary =>
      isDark ? AppColors.darkTextPrimary : AppColors.lightTextPrimary;
  Color get textSecondary =>
      isDark ? AppColors.darkTextSecondary : AppColors.lightTextSecondary;
  Color get textMuted =>
      isDark ? AppColors.darkTextMuted : AppColors.lightTextMuted;

  Color get dividerColor =>
      isDark ? AppColors.darkBorder : AppColors.lightBorder;
}

// ══════════════════════════════════════════════════════════
// SHADOWS
// ══════════════════════════════════════════════════════════
class AppShadows {
  static List<BoxShadow> card(bool isDark) => [
        // Ultra-compact bottom-right shadow
        BoxShadow(
          color: isDark
              ? Colors.black.withValues(alpha: 0.6)
              : const Color(0xFFBCC6D6).withValues(alpha: 0.8),
          blurRadius: 6,
          offset: const Offset(2, 2),
        ),
        // Ultra-compact top-left highlight
        BoxShadow(
          color: isDark ? Colors.white.withValues(alpha: 0.08) : Colors.white,
          blurRadius: 6,
          offset: const Offset(-2, -2),
        ),
      ];

  static List<BoxShadow> glow(Color color) => [
        BoxShadow(
          color: color.withValues(alpha: 0.3),
          blurRadius: 20,
          spreadRadius: 2,
        ),
      ];
}
