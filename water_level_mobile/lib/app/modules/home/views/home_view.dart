import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import '../../../routes/app_pages.dart';
import '../controllers/home_controller.dart';

class HomeView extends GetView<HomeController> {
  const HomeView({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'COMMAND CENTER',
              style: GoogleFonts.outfit(
                fontSize: 12,
                fontWeight: FontWeight.w900,
                color: const Color(0xFF64748B),
                letterSpacing: 2,
              ),
            ),
            Obx(() => Text(
              controller.isOnline.value ? 'SYSTEM ONLINE' : 'SYSTEM OFFLINE',
              style: GoogleFonts.outfit(
                fontSize: 10,
                fontWeight: FontWeight.bold,
                color: controller.isOnline.value ? const Color(0xFF10B981) : const Color(0xFFEF4444),
                letterSpacing: 1,
              ),
            )),
          ],
        ),
        actions: [
          IconButton(
            onPressed: () => Get.toNamed(Routes.SETTINGS),
            icon: const Icon(Icons.settings_outlined, color: Color(0xFF64748B)),
          ),
          const SizedBox(width: 8),
        ],
      ),
      body: Container(
        decoration: const BoxDecoration(
          color: Color(0xFFF8FAFC),
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              Color(0xFFF1F5F9),
              Color(0xFFE2E8F0),
              Color(0xFFF8FAFC),
            ],
          ),
        ),
        child: Stack(
          children: [
            // Decorative background elements
            Positioned(
              top: -100,
              right: -100,
              child: Container(
                width: 300,
                height: 300,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: const Color(0xFF3B82F6).withOpacity(0.05),
                ),
              ),
            ),

            SafeArea(
              child: Obx(() => SingleChildScrollView(
                padding: const EdgeInsets.symmetric(horizontal: 24),
                physics: const BouncingScrollPhysics(),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const SizedBox(height: 20),
                    
                    // 1. Primary Monitor Card (The Tank)
                    _buildHeroMonitor(context),
                    
                    const SizedBox(height: 24),
                    
                    // 2. Telemetry Grid
                    Row(
                      children: [
                        Expanded(
                          child: _buildGlassMetric(
                            'JARAK KE LUBER',
                            controller.distanceToGround.value.toStringAsFixed(1),
                            'CM',
                            Icons.water_damage_outlined,
                            controller.distanceToGround.value >= 0 ? const Color(0xFFEF4444) : const Color(0xFF6366F1),
                          ),
                        ),
                        const SizedBox(width: 16),
                        Expanded(
                          child: _buildGlassMetric(
                            'PREDIKSI MELUAP',
                            controller.etaOverflow.value.split(' ')[0],
                            'MENIT',
                            Icons.auto_graph,
                            const Color(0xFFF59E0B),
                          ),
                        ),
                      ],
                    ),
                    
                    const SizedBox(height: 24),
                    
                    // 3. System Status Bar
                    _buildSystemStatusBar(),
                    
                    const SizedBox(height: 32),
                    
                    // 4. Footer Branding
                    Center(
                      child: Column(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: Colors.white,
                              shape: BoxShape.circle,
                              border: Border.all(color: const Color(0xFFE2E8F0)),
                            ),
                            child: const Icon(Icons.security, size: 20, color: Color(0xFF64748B)),
                          ),
                          const SizedBox(height: 12),
                          Text(
                            'CYBERNOVA TELEMETRY v2.0',
                            style: GoogleFonts.outfit(
                              fontSize: 10,
                              fontWeight: FontWeight.w900,
                              color: const Color(0xFF94A3B8),
                              letterSpacing: 2,
                            ),
                          ),
                          const SizedBox(height: 40),
                        ],
                      ),
                    ),
                  ],
                ),
              )),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildHeroMonitor(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(40),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF1E293B).withOpacity(0.08),
            blurRadius: 40,
            offset: const Offset(0, 20),
          ),
        ],
      ),
      child: Column(
        children: [
          // Main Display Info
          Container(
            padding: const EdgeInsets.all(32),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.9),
              borderRadius: const BorderRadius.vertical(top: Radius.circular(40)),
              border: Border.all(color: Colors.white, width: 2),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    _buildStatusBadge(),
                    Text(
                      'TMA REAL-TIME',
                      style: GoogleFonts.outfit(
                        fontSize: 10,
                        fontWeight: FontWeight.w900,
                        color: const Color(0xFF64748B),
                        letterSpacing: 2,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 24),
                Row(
                  crossAxisAlignment: CrossAxisAlignment.baseline,
                  textBaseline: TextBaseline.alphabetic,
                  children: [
                    Text(
                      controller.waterLevel.value.toStringAsFixed(2),
                      style: GoogleFonts.rajdhani(
                        fontSize: 80,
                        fontWeight: FontWeight.w900,
                        color: const Color(0xFF1E293B),
                        height: 1,
                        letterSpacing: -4,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Text(
                      'm MDPL',
                      style: GoogleFonts.outfit(
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                        color: const Color(0xFF3B82F6),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    const Icon(Icons.location_on, size: 14, color: Color(0xFF94A3B8)),
                    const SizedBox(width: 4),
                    Text(
                      'SUNGAI CITARUM - SEKTOR KARAWANG',
                      style: GoogleFonts.outfit(
                        fontSize: 10,
                        fontWeight: FontWeight.bold,
                        color: const Color(0xFF94A3B8),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
          
          // The Visualizer (Injected into the card)
          _buildRiverVisualizer(context),
        ],
      ),
    );
  }

  Widget _buildStatusBadge() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
      decoration: BoxDecoration(
        color: Color(controller.statusColor.value).withOpacity(0.1),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Color(controller.statusColor.value).withOpacity(0.2)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 8,
            height: 8,
            decoration: BoxDecoration(
              color: Color(controller.statusColor.value),
              shape: BoxShape.circle,
              boxShadow: [
                BoxShadow(
                  color: Color(controller.statusColor.value).withOpacity(0.4),
                  blurRadius: 8,
                  spreadRadius: 2,
                ),
              ],
            ),
          ),
          const SizedBox(width: 10),
          Text(
            controller.statusSiaga.value,
            style: GoogleFonts.outfit(
              fontSize: 10,
              fontWeight: FontWeight.w900,
              color: Color(controller.statusColor.value),
              letterSpacing: 1.5,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildGlassMetric(String label, String value, String unit, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.8),
        borderRadius: BorderRadius.circular(32),
        border: Border.all(color: Colors.white, width: 2),
        boxShadow: [
          BoxShadow(
            color: color.withOpacity(0.05),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: color.withOpacity(0.1),
              borderRadius: BorderRadius.circular(14),
            ),
            child: Icon(icon, color: color, size: 20),
          ),
          const SizedBox(height: 20),
          Text(
            label,
            style: GoogleFonts.outfit(
              fontSize: 9,
              fontWeight: FontWeight.w900,
              color: const Color(0xFF94A3B8),
              letterSpacing: 1,
            ),
          ),
          const SizedBox(height: 8),
          Row(
            crossAxisAlignment: CrossAxisAlignment.baseline,
            textBaseline: TextBaseline.alphabetic,
            children: [
              Text(
                value,
                style: GoogleFonts.rajdhani(
                  fontSize: 32,
                  fontWeight: FontWeight.w900,
                  color: const Color(0xFF1E293B),
                ),
              ),
              const SizedBox(width: 4),
              Text(
                unit,
                style: GoogleFonts.outfit(
                  fontSize: 12,
                  fontWeight: FontWeight.bold,
                  color: const Color(0xFF64748B),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildSystemStatusBar() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
      decoration: BoxDecoration(
        color: const Color(0xFF1E293B),
        borderRadius: BorderRadius.circular(32),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF1E293B).withOpacity(0.2),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'UPDATE TERAKHIR',
                style: GoogleFonts.outfit(
                  fontSize: 8,
                  fontWeight: FontWeight.w900,
                  color: const Color(0xFF94A3B8),
                  letterSpacing: 1,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                controller.lastUpdated.value.isNotEmpty ? controller.lastUpdated.value : '--:--:--',
                style: GoogleFonts.outfit(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                ),
              ),
            ],
          ),
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.1),
              borderRadius: BorderRadius.circular(12),
            ),
            child: const Icon(Icons.refresh, color: Colors.white, size: 20),
          ),
        ],
      ),
    );
  }

  Widget _buildRiverVisualizer(BuildContext context) {
    double minMdpl = 12.0; // 2 Meter dari sensor (Kosong)
    double maxMdpl = 13.0; // 1 Meter dari sensor (Penuh/Luber)
    double currentMdpl = controller.waterLevel.value;
    double fillPercentage = ((currentMdpl - minMdpl) / (maxMdpl - minMdpl)).clamp(0.0, 1.0);

    return Container(
      height: 350,
      width: double.infinity,
      decoration: const BoxDecoration(
        color: Color(0xFFF1F5F9),
        borderRadius: BorderRadius.vertical(bottom: Radius.circular(40)),
      ),
      child: ClipRRect(
        borderRadius: const BorderRadius.vertical(bottom: Radius.circular(40)),
        child: Stack(
          alignment: Alignment.bottomCenter,
          children: [
            Positioned.fill(
              child: Padding(
                padding: const EdgeInsets.symmetric(vertical: 40),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: List.generate(7, (index) {
                    double val = 14.0 - index;
                    return Row(
                      children: [
                        const SizedBox(width: 24),
                        Container(width: 20, height: 1, color: const Color(0xFFCBD5E1)),
                        const SizedBox(width: 12),
                        Text(
                          val.toStringAsFixed(2),
                          style: GoogleFonts.rajdhani(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: const Color(0xFF94A3B8),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(child: Container(height: 1, color: const Color(0xFFE2E8F0).withOpacity(0.5))),
                      ],
                    );
                  }),
                ),
              ),
            ),
            Center(
              child: Opacity(
                opacity: 0.05,
                child: Text(
                  '${(fillPercentage * 100).toInt()}%',
                  style: GoogleFonts.rajdhani(
                    fontSize: 180,
                    fontWeight: FontWeight.w900,
                    color: const Color(0xFF1E293B),
                    letterSpacing: -10,
                  ),
                ),
              ),
            ),
            AnimatedContainer(
              duration: const Duration(seconds: 1),
              curve: Curves.easeOutCubic,
              height: 350 * fillPercentage,
              width: double.infinity,
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [
                    Color(0xFF38BDF8),
                    Color(0xFF1D4ED8),
                    Color(0xFF1E3A8A),
                  ],
                ),
              ),
              child: Stack(
                children: [
                  CustomPaint(
                    size: Size.infinite,
                    painter: WavePainter(const Color(0xFF38BDF8)),
                  ),
                  Center(
                    child: Text(
                      '${(fillPercentage * 100).toInt()}%',
                      style: GoogleFonts.rajdhani(
                        fontSize: 64,
                        fontWeight: FontWeight.w900,
                        color: Colors.white.withOpacity(0.4),
                      ),
                    ),
                  ),
                ],
              ),
            ),
            AnimatedPositioned(
              duration: const Duration(seconds: 1),
              curve: Curves.easeOutCubic,
              bottom: (350 * fillPercentage) - 1,
              left: 0,
              right: 0,
              child: Row(
                children: [
                  Expanded(
                    child: Container(
                      height: 2,
                      decoration: BoxDecoration(
                        color: Colors.white,
                        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.3), blurRadius: 10)],
                      ),
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                    decoration: BoxDecoration(
                      color: const Color(0xFF1E293B),
                      borderRadius: const BorderRadius.only(topLeft: Radius.circular(12), bottomLeft: Radius.circular(12)),
                    ),
                    child: Text(
                      '${currentMdpl.toStringAsFixed(2)} MDPL',
                      style: GoogleFonts.rajdhani(
                        fontSize: 12,
                        fontWeight: FontWeight.bold,
                        color: Colors.white,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class WavePainter extends CustomPainter {
  final Color baseColor;
  WavePainter(this.baseColor);

  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = Colors.white.withOpacity(0.15)
      ..style = PaintingStyle.fill;

    final path = Path();
    path.moveTo(0, 20);
    path.quadraticBezierTo(size.width * 0.25, 0, size.width * 0.5, 20);
    path.quadraticBezierTo(size.width * 0.75, 40, size.width, 20);
    path.lineTo(size.width, 0);
    path.lineTo(0, 0);
    path.close();

    canvas.drawPath(path, paint);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => true;
}
