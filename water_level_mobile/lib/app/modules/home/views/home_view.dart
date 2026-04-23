import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import '../controllers/home_controller.dart';

class HomeView extends GetView<HomeController> {
  const HomeView({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      body: Stack(
        children: [
          // Background accents (radial gradients)
          Positioned(
            top: -100,
            right: -50,
            child: Container(
              width: 300,
              height: 300,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: Colors.blue.withOpacity(0.05),
              ),
            ),
          ),
          Positioned(
            bottom: -50,
            left: -100,
            child: Container(
              width: 300,
              height: 300,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: Colors.cyan.withOpacity(0.05),
              ),
            ),
          ),
          
          SafeArea(
            child: Obx(() {
              if (controller.isLoading.value) {
                return const Center(
                  child: CircularProgressIndicator(color: Color(0xFF06B6D4)),
                );
              }

              return RefreshIndicator(
                onRefresh: controller.fetchData,
                color: const Color(0xFF06B6D4),
                child: ListView(
                  physics: const AlwaysScrollableScrollPhysics(),
                  padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 20),
                  children: [
                    _buildHeader(),
                    const SizedBox(height: 24),
                    _buildMainKpiCard(),
                    const SizedBox(height: 24),
                    _buildRiverVisualizer(context),
                  ],
                ),
              );
            }),
          ),
        ],
      ),
    );
  }

  Widget _buildHeader() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Water Monitoring',
              style: GoogleFonts.inter(
                fontSize: 22,
                fontWeight: FontWeight.bold,
                color: const Color(0xFF0F172A),
              ),
            ),
            Text(
              'Real-Time Dashboard',
              style: GoogleFonts.inter(
                fontSize: 14,
                color: const Color(0xFF64748B),
              ),
            ),
          ],
        ),
        Container(
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.03),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: const Icon(Icons.water_drop, color: Color(0xFF3B82F6)),
        )
      ],
    );
  }

  Widget _buildMainKpiCard() {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.65),
        borderRadius: BorderRadius.circular(32),
        border: Border.all(color: Colors.white, width: 2),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF3B82F6).withOpacity(0.05),
            blurRadius: 24,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(32),
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: 20, sigmaY: 20),
          child: Padding(
            padding: const EdgeInsets.all(28),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                      decoration: BoxDecoration(
                        color: const Color(0xFFEFF6FF),
                        borderRadius: BorderRadius.circular(20),
                        border: Border.all(color: const Color(0xFFDBEAFE)),
                      ),
                      child: Text(
                        'SEKTOR UTAMA',
                        style: GoogleFonts.inter(
                          fontSize: 10,
                          fontWeight: FontWeight.bold,
                          color: const Color(0xFF2563EB),
                          letterSpacing: 1,
                        ),
                      ),
                    ),
                    Row(
                      children: [
                        const Icon(Icons.verified_user, color: Color(0xFF10B981), size: 16),
                        const SizedBox(width: 4),
                        Text(
                          'Aman',
                          style: GoogleFonts.inter(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: const Color(0xFF10B981),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                Text(
                  'Terakhir diperbarui:',
                  style: GoogleFonts.inter(
                    fontSize: 12,
                    color: const Color(0xFF64748B),
                  ),
                ),
                Text(
                  controller.lastUpdated.value.isNotEmpty ? controller.lastUpdated.value : '--:--:--',
                  style: GoogleFonts.inter(
                    fontSize: 14,
                    fontWeight: FontWeight.bold,
                    color: const Color(0xFF334155),
                  ),
                ),
                const SizedBox(height: 32),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    // Water Level
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'KEDALAMAN AIR',
                          style: GoogleFonts.inter(
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                            color: const Color(0xFF64748B),
                            letterSpacing: 1,
                          ),
                        ),
                        Row(
                          crossAxisAlignment: CrossAxisAlignment.baseline,
                          textBaseline: TextBaseline.alphabetic,
                          children: [
                            TweenAnimationBuilder<double>(
                              tween: Tween<double>(begin: 0, end: controller.waterLevel.value),
                              duration: const Duration(seconds: 1),
                              builder: (context, value, child) {
                                return Text(
                                  value.toStringAsFixed(1),
                                  style: GoogleFonts.rajdhani(
                                    fontSize: 64,
                                    fontWeight: FontWeight.w900,
                                    color: const Color(0xFF1E293B),
                                    height: 1.1,
                                    letterSpacing: -1,
                                  ),
                                );
                              },
                            ),
                            const SizedBox(width: 4),
                            Text(
                              'CM',
                              style: GoogleFonts.inter(
                                fontSize: 20,
                                fontWeight: FontWeight.bold,
                                color: const Color(0xFF3B82F6),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                    // Distance
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        Text(
                          'JARAK UDARA',
                          style: GoogleFonts.inter(
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                            color: const Color(0xFF64748B),
                            letterSpacing: 1,
                          ),
                        ),
                        Row(
                          crossAxisAlignment: CrossAxisAlignment.baseline,
                          textBaseline: TextBaseline.alphabetic,
                          children: [
                            TweenAnimationBuilder<double>(
                              tween: Tween<double>(begin: 0, end: controller.distance.value),
                              duration: const Duration(seconds: 1),
                              builder: (context, value, child) {
                                return Text(
                                  value.toStringAsFixed(1),
                                  style: GoogleFonts.rajdhani(
                                    fontSize: 32,
                                    fontWeight: FontWeight.bold,
                                    color: const Color(0xFF94A3B8),
                                    height: 1.1,
                                  ),
                                );
                              },
                            ),
                            const SizedBox(width: 2),
                            Text(
                              'cm',
                              style: GoogleFonts.inter(
                                fontSize: 14,
                                color: const Color(0xFF94A3B8),
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildRiverVisualizer(BuildContext context) {
    // We assume tank max height is 200cm
    double maxTankHeight = 200.0;
    double currentLevel = controller.waterLevel.value;
    double fillPercentage = (currentLevel / maxTankHeight).clamp(0.0, 1.0);

    return Container(
      height: 400,
      decoration: BoxDecoration(
        color: const Color(0xFFF1F5F9), // Slight inner darkness
        borderRadius: BorderRadius.circular(40),
        border: Border.all(color: const Color(0xFFE2E8F0), width: 3),
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(37),
        child: Stack(
          alignment: Alignment.bottomCenter,
          children: [
            // Left & Right Banks
            Positioned(
              left: 0,
              top: 0,
              bottom: 0,
              width: 30,
              child: Container(
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    colors: [Color(0xFFF8FAFC), Color(0x33F8FAFC)],
                    begin: Alignment.centerLeft,
                    end: Alignment.centerRight,
                  ),
                  border: Border(right: BorderSide(color: Color(0xFFE2E8F0))),
                ),
              ),
            ),
            Positioned(
              right: 0,
              top: 0,
              bottom: 0,
              width: 30,
              child: Container(
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    colors: [Color(0x33F8FAFC), Color(0xFFF8FAFC)],
                    begin: Alignment.centerLeft,
                    end: Alignment.centerRight,
                  ),
                  border: Border(left: BorderSide(color: Color(0xFFE2E8F0))),
                ),
              ),
            ),

            // Text overlay behind water
            Positioned(
              top: 160,
              child: TweenAnimationBuilder<double>(
                tween: Tween<double>(begin: 0, end: fillPercentage * 100),
                duration: const Duration(seconds: 2),
                builder: (context, value, child) {
                  return Text(
                    '${value.toInt()}%',
                    style: GoogleFonts.inter(
                      fontSize: 100,
                      fontWeight: FontWeight.w900,
                      color: const Color(0xFF94A3B8).withOpacity(0.2),
                      letterSpacing: -4,
                    ),
                  );
                },
              ),
            ),

            // Water Layer
            AnimatedContainer(
              duration: const Duration(seconds: 2),
              curve: Curves.easeOutQuart,
              height: 400 * fillPercentage,
              width: double.infinity,
              decoration: const BoxDecoration(
                borderRadius: BorderRadius.vertical(bottom: Radius.circular(37)),
                gradient: LinearGradient(
                  begin: Alignment.centerLeft,
                  end: Alignment.centerRight,
                  colors: [
                    Color(0xFF0EA5E9),
                    Color(0xFF0284C7),
                    Color(0xFF0EA5E9),
                  ],
                ),
                border: Border(
                  top: BorderSide(color: Colors.white, width: 4),
                ),
                boxShadow: [
                  BoxShadow(
                    color: Color(0x800284C7),
                    blurRadius: 30,
                    offset: Offset(0, -6),
                  ),
                  BoxShadow(
                    color: Colors.white,
                    blurRadius: 15,
                    offset: Offset(0, -2),
                  ),
                ],
              ),
              child: Stack(
                children: [
                  // Inner glow
                  Positioned(
                    top: 0,
                    left: 0,
                    right: 0,
                    height: 20,
                    child: Container(
                      decoration: const BoxDecoration(
                        gradient: LinearGradient(
                          begin: Alignment.topCenter,
                          end: Alignment.bottomCenter,
                          colors: [Color(0x66FFFFFF), Colors.transparent],
                        ),
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
