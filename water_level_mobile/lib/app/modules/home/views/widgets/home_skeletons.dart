import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';

class HomeSkeletons extends StatelessWidget {
  final bool showScene;
  const HomeSkeletons({super.key, this.showScene = false});

  @override
  Widget build(BuildContext context) {
    if (showScene) {
      return Stack(
        children: [
          // 1. Scene Shimmer (Rectangle, No curve)
          _buildSkeletonImmersiveScene(),
          
          // 2. Content Shimmer (Starts with overlapping curve)
          Padding(
            padding: const EdgeInsets.only(top: 410), // Overlap a bit
            child: Container(
              width: double.infinity,
              decoration: const BoxDecoration(
                color: Colors.white, // This will be the "white" background
                borderRadius: BorderRadius.only(
                  topLeft: Radius.circular(32),
                  topRight: Radius.circular(32),
                ),
              ),
              child: _buildBodyContent(),
            ),
          ),
        ],
      );
    }

    // Default column if no scene
    return _buildBodyContent();
  }

  Widget _buildBodyContent() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 24, 20, 40),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // 1. Scene Metrics Row (3 Columns)
          Row(
            children: [
              Expanded(child: _buildSkeletonCard(height: 90)),
              const SizedBox(width: 12),
              Expanded(child: _buildSkeletonCard(height: 90)),
              const SizedBox(width: 12),
              Expanded(child: _buildSkeletonCard(height: 90)),
            ],
          ),
          const SizedBox(height: 24),
          
          // 2. Section Title
          _buildSkeletonSectionTitle(width: 140),
          const SizedBox(height: 16),
          
          // 3. Insights Grid (2x2)
          GridView.count(
            crossAxisCount: 2,
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            mainAxisSpacing: 16,
            crossAxisSpacing: 16,
            childAspectRatio: 1.35,
            children: List.generate(4, (index) => _buildSkeletonCard(height: 100)),
          ),
          const SizedBox(height: 24),
          
          // 4. Sparkline Section
          _buildSkeletonSectionTitle(width: 100),
          const SizedBox(height: 16),
          _buildSkeletonCard(height: 180),
          const SizedBox(height: 24),
          
          // 5. System Status
          _buildSkeletonSectionTitle(width: 120),
          const SizedBox(height: 16),
          _buildSkeletonCard(height: 140),
          const SizedBox(height: 24),
          
          // 6. Map Section
          _buildSkeletonSectionTitle(width: 150),
          const SizedBox(height: 16),
          _buildSkeletonCard(height: 200),
        ],
      ),
    );
  }

  Widget _buildSkeletonImmersiveScene() {
    return Shimmer.fromColors(
      baseColor: Colors.grey.withValues(alpha: 0.1),
      highlightColor: Colors.grey.withValues(alpha: 0.05),
      child: Container(
        width: double.infinity,
        height: 440,
        color: Colors.white,
      ),
    );
  }

  Widget _buildSkeletonSectionTitle({required double width}) {
    return Shimmer.fromColors(
      baseColor: Colors.grey.withValues(alpha: 0.1),
      highlightColor: Colors.grey.withValues(alpha: 0.05),
      child: Container(
        width: width,
        height: 12,
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(4),
        ),
      ),
    );
  }

  Widget _buildSkeletonCard({required double height}) {
    return Shimmer.fromColors(
      baseColor: Colors.grey.withValues(alpha: 0.1),
      highlightColor: Colors.grey.withValues(alpha: 0.05),
      child: Container(
        width: double.infinity,
        height: height,
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
        ),
      ),
    );
  }
}
