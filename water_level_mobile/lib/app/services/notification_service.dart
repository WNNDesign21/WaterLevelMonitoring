import 'dart:typed_data';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';

class NotificationService {
  static final FlutterLocalNotificationsPlugin _plugin =
      FlutterLocalNotificationsPlugin();

  static Future<void> init() async {
    if (kIsWeb) return;

    const android = AndroidInitializationSettings('@mipmap/ic_launcher');
    const ios = DarwinInitializationSettings(
      requestAlertPermission: true,
      requestBadgePermission: true,
      requestSoundPermission: true,
    );
    const settings = InitializationSettings(android: android, iOS: ios);
    await _plugin.initialize(settings);

    // Request permission on Android 13+
    if (defaultTargetPlatform == TargetPlatform.android) {
      await _plugin
          .resolvePlatformSpecificImplementation<
              AndroidFlutterLocalNotificationsPlugin>()
          ?.requestNotificationsPermission();
    }
  }

  static Future<void> showAlert({
    required int id,
    required String title,
    required String body,
    String? payload,
    NotificationImportance importance = NotificationImportance.info,
  }) async {
    if (kIsWeb) return;
    
    final androidDetails = AndroidNotificationDetails(
      'water_level_alerts',
      'Peringatan Ketinggian Air',
      channelDescription: 'Notifikasi status siaga ketinggian air sungai',
      importance: Importance.max,
      priority: Priority.high,
      color: importance == NotificationImportance.critical
          ? Colors.red
          : importance == NotificationImportance.warning
              ? Colors.orange
              : Colors.blue,
      playSound: true,
      enableVibration: true,
      vibrationPattern: importance == NotificationImportance.critical
          ? Int64List.fromList([0, 500, 200, 500, 200, 1000])
          : Int64List.fromList([0, 300, 200, 300]),
      icon: '@mipmap/ic_launcher',
      largeIcon: const DrawableResourceAndroidBitmap('@mipmap/ic_launcher'),
      styleInformation: BigTextStyleInformation(body),
      ticker: title,
    );

    const iosDetails = DarwinNotificationDetails(
      presentAlert: true,
      presentBadge: true,
      presentSound: true,
    );

    final details = NotificationDetails(android: androidDetails, iOS: iosDetails);
    await _plugin.show(id, title, body, details, payload: payload);
  }

  // ── Alert level helpers ──────────────────────────────────────────

  static Future<void> notifySiaga1() => showAlert(
    id: 1,
    title: '🚨 SIAGA 1 — BAHAYA!',
    body: 'Air sungai telah melewati batas bantaran! Segera evakuasi ke tempat yang lebih tinggi.',
    importance: NotificationImportance.critical,
  );

  static Future<void> notifySiaga2() => showAlert(
    id: 2,
    title: '⚠️ SIAGA 2 — WASPADA',
    body: 'Tinggi air mendekati batas bantaran. Pantau situasi dan bersiap untuk evakuasi.',
    importance: NotificationImportance.warning,
  );

  static Future<void> notifySiaga3() => showAlert(
    id: 3,
    title: '📡 SIAGA 3 — PANTAU',
    body: 'Tinggi air mengalami kenaikan signifikan. Status dimonitor secara aktif.',
    importance: NotificationImportance.info,
  );

  static Future<void> notifyAman() => showAlert(
    id: 4,
    title: '✅ Status: AMAN',
    body: 'Tinggi air kembali normal. Situasi terkendali.',
    importance: NotificationImportance.info,
  );
}

enum NotificationImportance { critical, warning, info }
