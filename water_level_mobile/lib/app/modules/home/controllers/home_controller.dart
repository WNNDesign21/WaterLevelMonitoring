import 'dart:async';
import 'package:get/get.dart';
import 'package:vibration/vibration.dart';
import 'package:flutter_ringtone_player/flutter_ringtone_player.dart';
import 'package:audioplayers/audioplayers.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:flutter/material.dart';
import '../../../data/providers/api_provider.dart';

class HomeController extends GetxController {
  final ApiProvider _apiProvider = ApiProvider();
  
  var isLoading = true.obs;
  var distance = 0.0.obs;
  var waterLevel = 0.0.obs;
  var validCount = 0.obs;
  var lastUpdated = ''.obs;
  
  // Additional Metrics
  var statusSiaga = 'AMAN'.obs;
  var statusColor = 0xFF10B981.obs; // Emerald
  var etaOverflow = '-- Menit'.obs;
  var distanceToGround = 0.0.obs; // Jarak air relatif ke tanah/tanggul
  var isOnline = false.obs;
  String _lastNotifiedStatus = 'AMAN';
  final AudioPlayer _audioPlayer = AudioPlayer();
  var isAlarmMuted = false.obs;
  Timer? _snoozeTimer;

  Timer? _timer;

  @override
  void onInit() {
    super.onInit();
    fetchData();
    // Auto-refresh setiap 5 detik
    _timer = Timer.periodic(const Duration(seconds: 5), (_) => fetchData());
  }

  @override
  void onClose() {
    _timer?.cancel();
    super.onClose();
  }

  Future<void> fetchData() async {
    try {
      final response = await _apiProvider.fetchLatestSensorData();
      if (response != null && response['status'] == 'success') {
        final data = response['data'];
        distance.value = (data['distance'] != null) ? double.parse(data['distance'].toString()) : 0.0;
        validCount.value = data['valid_count'] ?? 0;
        lastUpdated.value = data['created_at'] ?? '';
        isOnline.value = true;
        
        // Kalkulasi tinggi air (MDPL)
        waterLevel.value = 14.0 - (distance.value / 100.0);
        
        // Kalkulasi Jarak ke Tanah (Tanggul) untuk Simulasi Presentasi
        // Jarak sensor ke tanah ditetapkan 1 meter (100cm)
        distanceToGround.value = 100.0 - distance.value;

        // Update Status Siaga
        _updateStatusSiaga();
        
        // Simuasi AI ETA (Berdasarkan kecepatan kenaikan, mock logic)
        _updateAiEta();
      } else {
        isOnline.value = false;
      }
    } catch (e) {
      isOnline.value = false;
      print('Error fetching data: $e');
    } finally {
      isLoading.value = false;
    }
  }

  void _updateStatusSiaga() {
    // Threshold baru untuk PRESENTASI (Relatif terhadap 1 meter dari sensor)
    // Jika MDPL >= 13.00, berarti air sudah sejajar tanah (14.0 - 1.0)
    if (waterLevel.value >= 13.00) {
      statusSiaga.value = 'SIAGA 1';
      statusColor.value = 0xFFEF4444; // Red
    } else if (waterLevel.value >= 12.80) {
      statusSiaga.value = 'SIAGA 2';
      statusColor.value = 0xFFF59E0B; // Amber
    } else if (waterLevel.value >= 12.50) {
      statusSiaga.value = 'SIAGA 3';
      statusColor.value = 0xFF3B82F6; // Blue
    } else {
      statusSiaga.value = 'AMAN';
      statusColor.value = 0xFF10B981; // Emerald
    }

    _triggerIntenseAlert();
  }

  void _triggerIntenseAlert() async {
    // Hanya notifikasi jika status BERUBAH (misal: Aman -> Siaga 3)
    if (statusSiaga.value == _lastNotifiedStatus) return;

    _lastNotifiedStatus = statusSiaga.value;
    
    // Reset mute setiap kali status berubah (jika naik level bahaya)
    isAlarmMuted.value = false;
    _snoozeTimer?.cancel();

    // 1. Logika Suara & Popup (Prioritas Utama)
    if (statusSiaga.value == 'SIAGA 1') {
      _startSiaga1Alarm();
    } else if (statusSiaga.value == 'SIAGA 2') {
      _audioPlayer.play(UrlSource('https://assets.mixkit.co/active_storage/sfx/2552/2552-preview.mp3'));
      Get.snackbar(
        'WASPADA!',
        'Tinggi air mencapai SIAGA 2. Pantau terus!',
        backgroundColor: Colors.orange,
        colorText: Colors.white,
      );
    } else if (statusSiaga.value == 'SIAGA 3') {
      FlutterRingtonePlayer().playNotification();
      Get.snackbar(
        'MONITORING',
        'Tinggi air mencapai SIAGA 3.',
        backgroundColor: Colors.blue,
        colorText: Colors.white,
      );
    } else {
      _stopAllAlarms();
    }

    // 2. Logika Getaran (Hanya jika hardware tersedia)
    if (await Vibration.hasVibrator() ?? false) {
      if (statusSiaga.value == 'SIAGA 1') {
        Vibration.vibrate(pattern: [500, 200, 500, 200, 1000], intensities: [255, 255, 255, 255, 255]);
      } else if (statusSiaga.value == 'SIAGA 2') {
        Vibration.vibrate(pattern: [500, 300, 500]);
      } else if (statusSiaga.value == 'SIAGA 3') {
        Vibration.vibrate(duration: 500);
      }
    }
  }

  void _startSiaga1Alarm() {
    _audioPlayer.setReleaseMode(ReleaseMode.loop);
    _audioPlayer.play(UrlSource('https://assets.mixkit.co/active_storage/sfx/950/950-preview.mp3'));
    Vibration.vibrate(pattern: [500, 200, 500, 200, 1000], intensities: [255, 255, 255, 255, 255]);
    
    _showEvacuationDialog();
  }

  void _showEvacuationDialog() {
    Get.dialog(
      AlertDialog(
        backgroundColor: Colors.red.shade900,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        title: Column(
          children: [
            const Icon(Icons.warning_amber_rounded, color: Colors.white, size: 64),
            const SizedBox(height: 16),
            Text(
              'PERINGATAN EVAKUASI',
              textAlign: TextAlign.center,
              style: GoogleFonts.inter(fontWeight: FontWeight.w900, color: Colors.white),
            ),
          ],
        ),
        content: Text(
          'Sungai Citarum telah mencapai ambang batas SIAGA 1 (BAHAYA). Segera tinggalkan lokasi dan cari tempat yang lebih tinggi!',
          textAlign: TextAlign.center,
          style: GoogleFonts.inter(color: Colors.white70),
        ),
        actionsOverflowButtonSpacing: 10,
        actions: [
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: () {
                _stopAllAlarms();
                Get.back();
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.white,
                foregroundColor: Colors.red.shade900,
                padding: const EdgeInsets.symmetric(vertical: 16),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              child: const Text('SAYA SUDAH EVAKUASI (MATIKAN ALARM)', style: TextStyle(fontWeight: FontWeight.bold)),
            ),
          ),
          SizedBox(
            width: double.infinity,
            child: TextButton(
              onPressed: () {
                _stopAllAlarms();
                Get.back();
                _startSnooze();
              },
              child: const Text('INGATKAN 5 MENIT LAGI', style: TextStyle(color: Colors.white60)),
            ),
          ),
        ],
      ),
      barrierDismissible: false,
    );
  }

  void _startSnooze() {
    _snoozeTimer?.cancel();
    _snoozeTimer = Timer(const Duration(minutes: 5), () {
      if (statusSiaga.value == 'SIAGA 1') {
        _startSiaga1Alarm();
      }
    });
  }

  void _stopAllAlarms() {
    _audioPlayer.stop();
    FlutterRingtonePlayer().stop();
    Vibration.cancel();
  }

  void _updateAiEta() {
    if (waterLevel.value > 150) {
      etaOverflow.value = '12 Menit';
    } else if (waterLevel.value > 100) {
      etaOverflow.value = '45 Menit';
    } else {
      etaOverflow.value = '> 2 Jam';
    }
  }
}

