import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'dart:math' as math;
import '../../../core/theme/app_theme.dart';
import '../controllers/splash_controller.dart';

class SplashView extends GetView<SplashController> {
  const SplashView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: context.bgPrimary,
      body: Stack(
        children: [
          // 1. Dynamic Water Waves (3 Layers for Depth)
          Positioned(
            bottom: 0,
            left: 0,
            right: 0,
            height: 250,
            child: TweenAnimationBuilder<double>(
              duration: const Duration(seconds: 15),
              tween: Tween(begin: 0.0, end: 1.0),
              builder: (context, value, child) {
                return CustomPaint(
                  painter: _WavePainter(
                    progress: value,
                    color: AppColors.accent,
                  ),
                );
              },
            ),
          ),
          
          // 2. Centered Branding
          Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                // Simple Logo Entrance
                TweenAnimationBuilder<double>(
                  duration: const Duration(milliseconds: 1500),
                  tween: Tween(begin: 0.0, end: 1.0),
                  curve: Curves.easeOut,
                  builder: (context, value, child) {
                    return Opacity(
                      opacity: value,
                      child: Transform.scale(
                        scale: 0.9 + (0.1 * value),
                        child: child,
                      ),
                    );
                  },
                  child: Image.asset(
                    context.isDark ? 'assets/images/logo_dark.png' : 'assets/images/logo.png',
                    width: 140,
                    height: 140,
                    fit: BoxFit.contain,
                    errorBuilder: (context, error, stackTrace) =>
                        Icon(Icons.water_drop_rounded, size: 100, color: AppColors.accent),
                  ),
                ),
                
                const SizedBox(height: 32),
                
                // Minimalist Typography
                RichText(
                  text: TextSpan(
                    children: [
                      TextSpan(
                        text: 'Water',
                        style: GoogleFonts.inter(
                          fontSize: 40,
                          fontWeight: FontWeight.w900,
                          color: AppColors.accent,
                          letterSpacing: -1.0,
                        ),
                      ),
                      TextSpan(
                        text: 'Sense',
                        style: GoogleFonts.inter(
                          fontSize: 40,
                          fontWeight: FontWeight.w900,
                          color: context.textPrimary,
                          letterSpacing: -1.0,
                        ),
                      ),
                    ],
                  ),
                ),
                
                const SizedBox(height: 12),
                
                Text(
                  'SMART FLOOD MONITORING SYSTEM',
                  style: GoogleFonts.inter(
                    fontSize: 10,
                    fontWeight: FontWeight.w700,
                    color: context.textMuted.withValues(alpha: 0.4),
                    letterSpacing: 4.0,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _WavePainter extends CustomPainter {
  final double progress;
  final Color color;

  _WavePainter({required this.progress, required this.color});

  @override
  void paint(Canvas canvas, Size size) {
    final h = size.height;

    // Draw 3 layers of waves
    _drawWave(canvas, size, 
        h * 0.5, 25, progress * 2 * math.pi * 2, color.withValues(alpha: 0.1));
    _drawWave(canvas, size, 
        h * 0.6, 18, -progress * 2 * math.pi * 3 + math.pi/4, color.withValues(alpha: 0.2));
    _drawWave(canvas, size, 
        h * 0.7, 12, progress * 2 * math.pi * 1.5 + math.pi/2, color.withValues(alpha: 0.3));
  }

  void _drawWave(Canvas canvas, Size size, double baseHeight, double amplitude, double phase, Color waveColor) {
    final paint = Paint()
      ..color = waveColor
      ..style = PaintingStyle.fill;

    final path = Path();
    final w = size.width;
    final h = size.height;

    path.moveTo(0, baseHeight);
    for (double x = 0; x <= w; x++) {
      final y = baseHeight + 
                amplitude * math.sin((x / w * 2 * math.pi) + phase);
      path.lineTo(x, y);
    }
    path.lineTo(w, h);
    path.lineTo(0, h);
    path.close();
    canvas.drawPath(path, paint);
  }

  @override
  bool shouldRepaint(covariant _WavePainter oldDelegate) => true;
}
