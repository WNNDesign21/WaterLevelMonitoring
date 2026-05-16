import 'dart:async';
import 'package:audioplayers/audioplayers.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:vibration/vibration.dart';
import 'package:flutter_ringtone_player/flutter_ringtone_player.dart';
import 'package:get/get.dart';

class AlarmService extends GetxService {
  final AudioPlayer _audioPlayer = AudioPlayer();
  Timer? _siaga2Timer;
  Timer? _siaga3Timer;
  Timer? _snoozeTimer;

  Future<void> playSiaga1Alarm() async {
    try {
      await _audioPlayer.setReleaseMode(ReleaseMode.loop);
      await _audioPlayer.play(AssetSource('sounds/alarm_high.mp3'));
    } catch (e) {
      print("Audio Error (Siaga 1): $e");
    }
  }

  void startSiaga2Periodic(Function onPlay) {
    _siaga2Timer?.cancel();
    onPlay();
    _siaga2Timer = Timer.periodic(const Duration(seconds: 2), (timer) => onPlay());
  }

  void startSiaga3Periodic(Function onPlay) {
    _siaga3Timer?.cancel();
    onPlay();
    _siaga3Timer = Timer.periodic(const Duration(minutes: 5), (timer) => onPlay());
  }

  void stopAllAlarms() {
    _audioPlayer.stop();
    _siaga2Timer?.cancel();
    _siaga3Timer?.cancel();
    _snoozeTimer?.cancel();
    if (!kIsWeb) {
      FlutterRingtonePlayer().stop();
      Vibration.cancel();
    }
  }

  void startSnooze(Duration duration, Function onElapsed) {
    _snoozeTimer?.cancel();
    _snoozeTimer = Timer(duration, () => onElapsed());
  }

  Future<void> playWarningSound() async {
    try {
      await _audioPlayer.setReleaseMode(ReleaseMode.release);
      await _audioPlayer.play(AssetSource('sounds/notification_warning.mp3'));
    } catch (e) {
      print("Audio Error: $e");
    }
  }

  @override
  void onClose() {
    stopAllAlarms();
    _audioPlayer.dispose();
    super.onClose();
  }
}
