import 'dart:math' as math;
import 'dart:ui' as ui;
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

/// Paints a unified, full-width immersive flood scene with high-density technical ruler and precision-aligned status badge.
class FloodScenePainter extends CustomPainter {
  final double fillRatio;
  final double wavePhase;
  final Color waterColor;
  final Color statusColor;
  final bool isDark;
  final int weatherCode;
  final double tempC;
  final String locationName;
  final String weatherDesc;
  final ui.Image? cityImage;
  final bool showEnvironment;
  final bool showTank;
  final String statusText;
  final bool isOnline;
  final double waterLevel;

  final double elevationMdpl;
  final double sensorToBank;
  final double riverDepth;

  FloodScenePainter({
    required this.fillRatio,
    required this.wavePhase,
    required this.waterColor,
    required this.statusColor,
    required this.isDark,
    required this.weatherCode,
    required this.tempC,
    required this.locationName,
    required this.weatherDesc,
    required this.statusText,
    required this.isOnline,
    required this.waterLevel,
    required this.elevationMdpl,
    required this.sensorToBank,
    required this.riverDepth,
    this.cityImage,
    this.showEnvironment = true,
    this.showTank = true,
  });

  @override
  void paint(Canvas canvas, Size size) {
    final w = size.width;
    final h = size.height;

    final topH =
        (showEnvironment && showTank) ? h * 0.45 : (showEnvironment ? h : 0.0);
    final bottomH = h - topH;

    _drawAtmosphereBackground(canvas, size);

    if (showEnvironment) {
      _drawCity(canvas, Size(w, topH));
      _drawAtmosphereEffects(canvas, Size(w, topH), Offset(w * 0.85, 85));
    }

    if (showTank) {
      final bankLevelMdpl = elevationMdpl - (sensorToBank / 100.0);
      final riverBedMdpl = bankLevelMdpl - (riverDepth / 100.0);
      final double techFill =
          ((waterLevel - riverBedMdpl) / (bankLevelMdpl - riverBedMdpl))
              .clamp(0.0, 1.0);

      _drawWater(canvas, Offset(0, topH), Size(w, bottomH), techFill);
      _drawTechnicalOverlay(canvas, Offset(0, topH), Size(w, bottomH), techFill,
          bankLevelMdpl, riverBedMdpl);
    }

    if (showEnvironment) {
      _drawWeatherForeground(canvas, size);
    }
  }

  void _drawAtmosphereBackground(Canvas canvas, Size size) {
    final skyColors = isDark
        ? [
            const Color(0xFF020617), // Deepest Midnight
            const Color(0xFF0F172A), // Dark Slate Blue
            const Color(0xFF1E293B), // Soft Horizon Blue
          ]
        : [
            const Color(0xFF0369A1), // Deep Azure
            const Color(0xFF0EA5E9), // Sky Blue
            const Color(0xFFBAE6FD), // Soft Horizon Light
          ];

    canvas.drawRect(
      Rect.fromLTWH(0, 0, size.width, size.height),
      Paint()
        ..shader = LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: skyColors,
        ).createShader(Rect.fromLTWH(0, 0, size.width, size.height)),
    );
  }

  void _drawCity(Canvas canvas, Size size) {
    if (cityImage == null) return;

    final w = size.width;
    final h = size.height;
    final imgW = cityImage!.width.toDouble();
    final imgH = cityImage!.height.toDouble();

    // Calculate scale to fit width while maintaining aspect ratio
    final double scale = w / imgW;
    final double drawW = w;
    final double drawH = imgH * scale;

    // Position at the bottom of the atmosphere section
    // We add a slight offset (+10) to overlap with the water for a seamless transition
    canvas.drawImageRect(
      cityImage!,
      Rect.fromLTWH(0, 0, imgW, imgH),
      Rect.fromLTWH(0, h - drawH + 10, drawW, drawH),
      Paint()
        ..color = Colors.white.withValues(alpha: isDark ? 0.35 : 0.85)
        ..filterQuality = ui.FilterQuality.high, // Ensure high quality scaling
    );
  }

  void _drawWater(Canvas canvas, Offset offset, Size size, double customFill) {
    final w = size.width;
    final h = size.height;
    final startY = offset.dy;
    final waterY = startY + h * (1.0 - customFill);

    canvas.drawRect(
      Rect.fromLTWH(0, startY, w, h),
      Paint()..color = waterColor.withValues(alpha: 0.05),
    );

    final wavePath = Path();
    wavePath.moveTo(0, waterY);
    for (double x = 0; x <= w; x += 3) {
      final y = waterY + 6 * math.sin((x / w) * 2 * math.pi + wavePhase);
      wavePath.lineTo(x, y);
    }
    wavePath.lineTo(w, size.height + offset.dy);
    wavePath.lineTo(0, size.height + offset.dy);
    wavePath.close();

    final waterGrad = LinearGradient(
      begin: Alignment.topCenter,
      end: Alignment.bottomCenter,
      colors: [
        waterColor.withValues(alpha: 0.8),
        waterColor.withValues(alpha: 0.95),
      ],
    );

    canvas.drawPath(
        wavePath,
        Paint()
          ..shader = waterGrad.createShader(Rect.fromLTWH(0, waterY, w, h)));

    // Surface Reflection Glow
    final surfaceGlow = Paint()
      ..shader = LinearGradient(
        begin: Alignment.topCenter,
        end: Alignment.bottomCenter,
        colors: [Colors.white.withValues(alpha: 0.2), Colors.transparent],
      ).createShader(Rect.fromLTWH(0, waterY, w, 60));
    canvas.drawPath(wavePath, surfaceGlow);

    canvas.drawPath(
      wavePath,
      Paint()
        ..color = Colors.white.withValues(alpha: 0.4)
        ..style = PaintingStyle.stroke
        ..strokeWidth = 2.5
        ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 2),
    );

    // Subtle Micro-particles in water
    final pRng = math.Random(10);
    final pPaint = Paint()..color = Colors.white.withValues(alpha: 0.05);
    for (int i = 0; i < 15; i++) {
      final px = pRng.nextDouble() * w;
      final py = waterY + pRng.nextDouble() * (startY + h - waterY);
      canvas.drawCircle(Offset(px, py), 1.0, pPaint);
    }
  }

  void _drawTechnicalOverlay(Canvas canvas, Offset offset, Size size,
      double customFill, double bankMdpl, double bedMdpl) {
    final w = size.width;
    final h = size.height;

    // 1. Draw Tank Inner Shadow (Subtle Edges)
    final shadowWidth = 60.0;

    // Left Shadow
    canvas.drawRect(
        Rect.fromLTWH(0, offset.dy, shadowWidth, h),
        Paint()
          ..shader = LinearGradient(
            begin: Alignment.centerLeft,
            end: Alignment.centerRight,
            colors: [Colors.black.withValues(alpha: 0.07), Colors.transparent],
          ).createShader(Rect.fromLTWH(0, offset.dy, shadowWidth, h)));

    // Right Shadow
    canvas.drawRect(
        Rect.fromLTWH(w - shadowWidth, offset.dy, shadowWidth, h),
        Paint()
          ..shader = LinearGradient(
            begin: Alignment.centerRight,
            end: Alignment.centerLeft,
            colors: [Colors.black.withValues(alpha: 0.07), Colors.transparent],
          ).createShader(
              Rect.fromLTWH(w - shadowWidth, offset.dy, shadowWidth, h)));

    // 2. Technical Grid (Lines) - ULTRA SUBTLE
    final gridPaint = Paint()
      ..color = Colors.white.withValues(alpha: 0.05)
      ..strokeWidth = 0.8;

    const step = 45.0;
    for (double x = 0; x <= w; x += step) {
      canvas.drawLine(
          Offset(x, offset.dy), Offset(x, offset.dy + h), gridPaint);
    }
    for (double y = offset.dy; y <= offset.dy + h; y += step) {
      canvas.drawLine(Offset(0, y), Offset(w, y), gridPaint);
    }

    // 3. Side "Glass" Borders
    final glassPaint = Paint()
      ..color = Colors.white.withValues(alpha: 0.06)
      ..strokeWidth = 2.0;
    canvas.drawLine(Offset(2, offset.dy), Offset(2, offset.dy + h), glassPaint);
    canvas.drawLine(
        Offset(w - 2, offset.dy), Offset(w - 2, offset.dy + h), glassPaint);

    _drawCentralData(canvas, offset, size, customFill);
    _drawRuler(canvas, offset, size, customFill, bankMdpl, bedMdpl);
  }

  void _drawCentralData(
      Canvas canvas, Offset offset, Size size, double customFill) {
    final w = size.width;
    final h = size.height;
    final targetY = offset.dy + h / 2 - 10;

    // Display "---" if offline
    final String percentText =
        isOnline ? '${(customFill * 100).toInt()}%' : '---';

    final percentTp = TextPainter(
      text: TextSpan(
        text: percentText,
        style: GoogleFonts.inter(
          fontSize: 72,
          fontWeight: FontWeight.w900,
          color: Colors.white.withValues(alpha: 0.35),
        ),
      ),
      textDirection: TextDirection.ltr,
    );
    percentTp.layout();
    percentTp.paint(canvas,
        Offset((w - percentTp.width) / 2, targetY - percentTp.height / 2));

    _drawEnhancedStatusBadge(canvas, w, targetY - percentTp.height / 2 - 35);

    // Display "STALE" or current MDPL with indicator
    final String mdplText = isOnline
        ? '${waterLevel.toStringAsFixed(2)} m (MDPL)'
        : 'LAST KNOWN: ${waterLevel.toStringAsFixed(2)} m';

    final mdplTp = TextPainter(
      text: TextSpan(
        text: mdplText,
        style: GoogleFonts.rajdhani(
          fontSize: isOnline ? 18 : 14,
          fontWeight: FontWeight.w800,
          color: Colors.white.withValues(alpha: 0.3),
        ),
      ),
      textDirection: TextDirection.ltr,
    );
    mdplTp.layout();
    mdplTp.paint(
        canvas, Offset((w - mdplTp.width) / 2, targetY + percentTp.height / 2));
  }

  void _drawEnhancedStatusBadge(Canvas canvas, double w, double y) {
    IconData iconData = Icons.check_circle_rounded;
    if (statusText.contains('OFFLINE')) {
      iconData = Icons.signal_wifi_off_rounded;
    } else if (statusText.contains('SIAGA 3')) {
      iconData = Icons.info_rounded;
    } else if (statusText.contains('SIAGA 2')) {
      iconData = Icons.warning_rounded;
    } else if (statusText.contains('SIAGA 1')) {
      iconData = Icons.dangerous_rounded;
    } else if (statusText.contains('LUBER')) {
      iconData = Icons.water_damage_rounded;
    }

    final iconTp = TextPainter(
      text: TextSpan(
        text: String.fromCharCode(iconData.codePoint),
        style: TextStyle(
          fontSize: 18,
          fontFamily: iconData.fontFamily,
          package: iconData.fontPackage,
          color: Colors.white,
        ),
      ),
      textDirection: TextDirection.ltr,
    );
    iconTp.layout();

    final textTp = TextPainter(
      text: TextSpan(
        text: statusText,
        style: GoogleFonts.inter(
          fontSize: 15,
          fontWeight: FontWeight.w900,
          color: Colors.white.withValues(alpha: 0.9),
          letterSpacing: 1.5,
        ),
      ),
      textDirection: TextDirection.ltr,
    );
    textTp.layout();

    final spacing = 8.0;
    final badgeW = iconTp.width + textTp.width + spacing + 32;
    final badgeH = 36.0;

    final statusBoxRect = RRect.fromRectAndRadius(
      Rect.fromCenter(
        center: Offset(w / 2, y),
        width: badgeW,
        height: badgeH,
      ),
      const Radius.circular(30),
    );

    canvas.drawRRect(
        statusBoxRect, Paint()..color = Colors.black.withValues(alpha: 0.25));
    canvas.drawRRect(
        statusBoxRect,
        Paint()
          ..color = Colors.white.withValues(alpha: 0.15)
          ..style = PaintingStyle.stroke
          ..strokeWidth = 1.2);

    final totalContentW = iconTp.width + spacing + textTp.width;
    final startX = w / 2 - totalContentW / 2;

    iconTp.paint(canvas, Offset(startX, y - iconTp.height / 2));
    textTp.paint(
        canvas, Offset(startX + iconTp.width + spacing, y - textTp.height / 2));
  }

  void _drawRuler(Canvas canvas, Offset offset, Size size, double customFill,
      double bankMdpl, double bedMdpl) {
    final h = size.height;
    final w = size.width;
    final startY = offset.dy;
    final waterY = startY + h * (1.0 - customFill);
    final rulerX = 60.0;

    final double range = bankMdpl - bedMdpl;
    final pixelsPerMeter = h / range;

    final linePaint = Paint()
      ..color = Colors.white.withValues(alpha: 0.2)
      ..strokeWidth = 1.2;
    final majorLinePaint = Paint()
      ..color = Colors.white.withValues(alpha: 0.5)
      ..strokeWidth = 2.5;

    final textStyle = GoogleFonts.inter(
      fontSize: 11,
      fontWeight: FontWeight.w800,
      color: Colors.white.withValues(alpha: 0.7),
    );

    for (double v = bedMdpl; v <= bankMdpl + 0.01; v += 0.25) {
      final y = startY + (bankMdpl - v) * pixelsPerMeter;
      if (y < startY - 5 || y > startY + h + 5) continue;

      bool isMajor = (v * 4).round() % 4 == 0;
      bool isSemiMajor = (v * 4).round() % 2 == 0;

      final double markLen = isMajor ? 25.0 : (isSemiMajor ? 15.0 : 8.0);
      final paint = isMajor ? majorLinePaint : linePaint;

      canvas.drawLine(Offset(rulerX, y), Offset(rulerX + markLen, y), paint);

      final tp = TextPainter(
        text: TextSpan(
          text: v.toStringAsFixed(2),
          style: textStyle.copyWith(
            fontSize: isMajor ? 11 : 9.5,
            color: isMajor
                ? Colors.white.withValues(alpha: 0.5)
                : Colors.white.withValues(alpha: 0.2),
          ),
        ),
        textDirection: TextDirection.ltr,
      );
      tp.layout();
      tp.paint(canvas, Offset(rulerX - tp.width - 10, y - tp.height / 2));
    }

    final surfacePaint = Paint()
      ..color = isOnline ? Colors.white : Colors.white.withValues(alpha: 0.4)
      ..strokeWidth = 3.5
      ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 1);

    canvas.drawLine(
        Offset(rulerX - 5, waterY), Offset(rulerX + 45, waterY), surfacePaint);

    _drawSurfaceBadge(canvas, w, waterY, waterLevel);
  }

  void _drawSurfaceBadge(Canvas canvas, double w, double y, double value) {
    final badgeWidth = 65.0;
    final badgeHeight = 24.0;
    final badgeX = w - badgeWidth - 10;

    final rrect = RRect.fromRectAndRadius(
      Rect.fromLTWH(badgeX, y - badgeHeight / 2, badgeWidth, badgeHeight),
      Radius.circular(badgeHeight / 2),
    );

    canvas.drawRRect(
      rrect.shift(const Offset(0, 2)),
      Paint()
        ..color = Colors.black.withValues(alpha: 0.3)
        ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 6),
    );

    canvas.drawRRect(
        rrect, Paint()..color = isOnline ? statusColor : Colors.blueGrey);

    canvas.drawRRect(
      rrect,
      Paint()
        ..color = Colors.white.withValues(alpha: 0.5)
        ..style = PaintingStyle.stroke
        ..strokeWidth = 1.5,
    );

    final String text = isOnline ? '${value.toStringAsFixed(2)} m' : '---';

    final tp = TextPainter(
      text: TextSpan(
        text: text,
        style: GoogleFonts.rajdhani(
          fontSize: 13,
          fontWeight: FontWeight.w900,
          color: Colors.white,
        ),
      ),
      textDirection: TextDirection.ltr,
    );
    tp.layout();
    tp.paint(canvas,
        Offset(badgeX + (badgeWidth - tp.width) / 2, y - tp.height / 2));
  }

  void _drawAtmosphereEffects(Canvas canvas, Size size, Offset center) {
    if (weatherCode >= 50) return;
    if (!isDark) {
      final sunPaint = Paint()
        ..color = Colors.yellow.withValues(alpha: 0.2)
        ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 30);
      canvas.drawCircle(center, 35, sunPaint);
    } else {
      final moonPaint = Paint()
        ..color = Colors.white.withValues(alpha: 0.15)
        ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 20);
      canvas.drawCircle(center, 25, moonPaint);
      canvas.drawCircle(
          center, 18, Paint()..color = Colors.white.withValues(alpha: 0.9));
    }
  }

  void _drawWeatherForeground(Canvas canvas, Size size) {
    // Rain animation removed as per request
  }

  @override
  bool shouldRepaint(covariant FloodScenePainter old) => true;
}
