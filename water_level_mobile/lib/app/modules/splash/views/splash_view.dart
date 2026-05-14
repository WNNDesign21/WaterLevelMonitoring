import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'dart:math' as math;
import '../controllers/splash_controller.dart';

class SplashView extends GetView<SplashController> {
  const SplashView({super.key});

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final bgColor = isDark ? const Color(0xFF0F172A) : Colors.white;
    final textColor = isDark ? Colors.white : const Color(0xFF1E293B);
    final logoAsset = isDark ? 'assets/images/logo_dark.png' : 'assets/images/logo.png';
    final waveColor = const Color(0xFF4F7EF8).withOpacity(isDark ? 0.4 : 0.3);

    return Scaffold(
      backgroundColor: bgColor,
      body: Stack(
        children: [
          // Bottom Wave Animation
          Positioned(
            bottom: 0,
            left: 0,
            right: 0,
            height: 200,
            child: TweenAnimationBuilder<double>(
              duration: const Duration(seconds: 5),
              tween: Tween(begin: 0.0, end: 1.0),
              builder: (context, value, child) {
                return CustomPaint(
                  painter: _WavePainter(
                    progress: value,
                    color: waveColor,
                  ),
                );
              },
              onEnd: () {}, // Handled by controller timer
            ),
          ),
          
          Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                // Logo with Clean Fade
                TweenAnimationBuilder<double>(
                  duration: const Duration(milliseconds: 1200),
                  tween: Tween(begin: 0.0, end: 1.0),
                  curve: Curves.easeOutCubic,
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
                    logoAsset,
                    width: 140,
                    height: 140,
                    fit: BoxFit.contain,
                    errorBuilder: (context, error, stackTrace) =>
                        Icon(Icons.water_drop_rounded, size: 100, color: const Color(0xFF4F7EF8)),
                  ),
                ),
                
                const SizedBox(height: 32),
                
                // App Name with Dual Colors
                TweenAnimationBuilder<double>(
                  duration: const Duration(milliseconds: 1000),
                  tween: Tween(begin: 0.0, end: 1.0),
                  builder: (context, value, child) {
                    return Opacity(
                      opacity: value,
                      child: child,
                    );
                  },
                  child: RichText(
                    text: TextSpan(
                      children: [
                        TextSpan(
                          text: 'Water',
                          style: GoogleFonts.inter(
                            fontSize: 36,
                            fontWeight: FontWeight.w900,
                            color: const Color(0xFF4F7EF8), // Dashboard Accent Blue (Royal Blue)
                            letterSpacing: -0.5,
                          ),
                        ),
                        TextSpan(
                          text: 'Sense',
                          style: GoogleFonts.inter(
                            fontSize: 36,
                            fontWeight: FontWeight.w900,
                            color: textColor,
                            letterSpacing: -0.5,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                
                const SizedBox(height: 8),
                
                // Subtitle
                TweenAnimationBuilder<double>(
                  duration: const Duration(milliseconds: 1000),
                  tween: Tween(begin: 0.0, end: 1.0),
                  builder: (context, value, child) {
                    return Opacity(
                      opacity: value,
                      child: child,
                    );
                  },
                  child: Text(
                    'SMART HYDROLOGY SYSTEM',
                    style: GoogleFonts.inter(
                      fontSize: 10,
                      fontWeight: FontWeight.w600,
                      color: textColor.withOpacity(0.4),
                      letterSpacing: 4.0,
                    ),
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
    final paint = Paint()
      ..color = color
      ..style = PaintingStyle.fill;

    final path = Path();
    final h = size.height;
    final w = size.width;

    path.moveTo(0, h * 0.5);
    
    for (double x = 0; x <= w; x++) {
      final y = h * 0.5 + 
                15 * math.sin((x / w * 2 * math.pi) + (progress * 2 * math.pi * 2));
      path.lineTo(x, y);
    }

    path.lineTo(w, h);
    path.lineTo(0, h);
    path.close();

    canvas.drawPath(path, paint);
    
    // Draw secondary wave for more depth
    final path2 = Path();
    final paint2 = Paint()..color = color.withOpacity(color.opacity * 0.5);
    
    path2.moveTo(0, h * 0.6);
    for (double x = 0; x <= w; x++) {
      final y = h * 0.6 + 
                12 * math.sin((x / w * 2 * math.pi) - (progress * 2 * math.pi * 1.5) + math.pi);
      path2.lineTo(x, y);
    }
    path2.lineTo(w, h);
    path2.lineTo(0, h);
    path2.close();
    
    canvas.drawPath(path2, paint2);
  }

  @override
  bool shouldRepaint(covariant _WavePainter oldDelegate) => true;
}
