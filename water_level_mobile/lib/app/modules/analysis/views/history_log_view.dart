import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:intl/intl.dart';
import '../../../core/theme/app_theme.dart';
import '../controllers/analysis_controller.dart';

class HistoryLogView extends GetView<AnalysisController> {
  const HistoryLogView({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: context.bgPrimary,
      appBar: AppBar(
        backgroundColor: context.bgCard,
        elevation: 0,
        title: Text(
          'RIWAYAT LENGKAP',
          style: GoogleFonts.inter(
            fontSize: 14,
            fontWeight: FontWeight.w900,
            color: context.textPrimary,
            letterSpacing: 1,
          ),
        ),
        leading: IconButton(
          icon: Icon(Icons.arrow_back_ios_new_rounded, color: context.textPrimary, size: 20),
          onPressed: () => Get.back(),
        ),
        actions: [
          Padding(
            padding: const EdgeInsets.symmetric(vertical: 10),
            child: ElevatedButton.icon(
              onPressed: () => controller.exportHistory(),
              icon: const Icon(Icons.file_download_outlined, size: 16, color: Colors.white),
              label: Text(
                'REPORT',
                style: GoogleFonts.inter(
                  fontSize: 10,
                  fontWeight: FontWeight.w800,
                  color: Colors.white,
                  letterSpacing: 0.5,
                ),
              ),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.accent,
                foregroundColor: Colors.white,
                elevation: 0,
                padding: const EdgeInsets.symmetric(horizontal: 12),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
            ),
          ),
          const SizedBox(width: 16),
        ],
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(70),
          child: Container(
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 12),
            child: Row(
              children: [
                Expanded(
                  child: Container(
                    decoration: BoxDecoration(
                      color: context.isDark
                          ? Colors.white.withValues(alpha: 0.05)
                          : Colors.black.withValues(alpha: 0.05),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: TextField(
                      onChanged: (value) => controller.searchQuery.value = value,
                      style: GoogleFonts.inter(
                        fontSize: 13,
                        color: context.textPrimary,
                      ),
                      decoration: InputDecoration(
                        hintText: 'Cari waktu, status, atau level...',
                        hintStyle: GoogleFonts.inter(
                          fontSize: 12,
                          color: context.textMuted,
                        ),
                        prefixIcon: Icon(Icons.search_rounded,
                            size: 18, color: context.textMuted),
                        border: InputBorder.none,
                        contentPadding:
                            const EdgeInsets.symmetric(vertical: 12),
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                GestureDetector(
                  onTap: () => controller.selectDateRange(context),
                  child: Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: AppColors.accent.withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Icon(Icons.tune_rounded,
                        size: 20, color: AppColors.accent),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
      body: Obx(() {
        final logs = controller.filteredHistoryData;
        if (logs.isEmpty) {
          return Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.history_rounded,
                    size: 64, color: context.dividerColor),
                const SizedBox(height: 16),
                Text(
                  controller.searchQuery.isEmpty
                      ? 'Tidak ada data log ditemukan'
                      : 'Data tidak ditemukan untuk "${controller.searchQuery.value}"',
                  style: GoogleFonts.inter(
                    color: context.textMuted,
                    fontWeight: FontWeight.w600,
                  ),
                  textAlign: TextAlign.center,
                ),
              ],
            ),
          );
        }

        return ListView.separated(
          physics: const BouncingScrollPhysics(),
          padding: const EdgeInsets.all(16),
          itemCount: logs.length,
          separatorBuilder: (context, index) => const SizedBox(height: 12),
          itemBuilder: (context, index) {
            final data = logs[index];
            return _buildDetailedHistoryCard(context, data);
          },
        );
      }),
    );
  }

  Widget _buildDetailedHistoryCard(BuildContext context, Map<String, dynamic> data) {
    final time = DateFormat('HH:mm:ss').format(data['time']);
    final date = DateFormat('EEEE, dd MMMM yyyy', 'id_ID').format(data['time']);
    final isAman = data['status'] == 'Aman';

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
        color: context.bgCard,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: context.borderColor),
      ),
      child: Column(
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                decoration: BoxDecoration(
                  color: AppColors.accent.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(4),
                ),
                child: Text(
                  time,
                  style: GoogleFonts.rajdhani(
                    fontSize: 12,
                    fontWeight: FontWeight.w800,
                    color: AppColors.accent,
                  ),
                ),
              ),
              const SizedBox(width: 8),
              Text(
                date,
                style: GoogleFonts.inter(
                  fontSize: 10,
                  fontWeight: FontWeight.w600,
                  color: context.textMuted,
                ),
              ),
              const Spacer(),
              _statusBadge(isAman, data['status']),
            ],
          ),
          const SizedBox(height: 10),
          Row(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              Expanded(
                flex: 2,
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.baseline,
                  textBaseline: TextBaseline.alphabetic,
                  children: [
                    Text(
                      data['level'].toStringAsFixed(2),
                      style: GoogleFonts.rajdhani(
                        fontSize: 22,
                        fontWeight: FontWeight.w900,
                        color: context.textPrimary,
                      ),
                    ),
                    const SizedBox(width: 4),
                    Text(
                      'm',
                      style: GoogleFonts.inter(
                        fontSize: 10,
                        fontWeight: FontWeight.w700,
                        color: context.textMuted,
                      ),
                    ),
                  ],
                ),
              ),
              Expanded(
                flex: 3,
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.end,
                  children: [
                    _buildStatMiniCompact(context, 'UP', data['max'] ?? 0.0, AppColors.statusSiaga1),
                    const SizedBox(width: 12),
                    _buildStatMiniCompact(context, 'DOWN', data['min'] ?? 0.0, AppColors.statusSafe),
                  ],
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildStatMiniCompact(BuildContext context, String label, double value, Color color) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        Row(
          children: [
            Icon(label == 'UP' ? Icons.trending_up_rounded : Icons.trending_down_rounded, 
                 size: 8, color: color.withValues(alpha: 0.7)),
            const SizedBox(width: 2),
            Text(
              label,
              style: GoogleFonts.inter(
                fontSize: 7,
                fontWeight: FontWeight.w800,
                color: context.textMuted,
                letterSpacing: 0.5,
              ),
            ),
          ],
        ),
        Text(
          '${value.toStringAsFixed(2)}m',
          style: GoogleFonts.rajdhani(
            fontSize: 13,
            fontWeight: FontWeight.w700,
            color: context.textPrimary,
          ),
        ),
      ],
    );
  }

  Widget _statusBadge(bool isAman, String status) {
    final color = isAman ? AppColors.statusSafe : AppColors.statusSiaga1;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(4),
        border: Border.all(color: color.withValues(alpha: 0.2), width: 0.5),
      ),
      child: Text(
        status.toUpperCase(),
        style: GoogleFonts.inter(
          fontSize: 8,
          fontWeight: FontWeight.w900,
          color: color,
        ),
      ),
    );
  }
}
