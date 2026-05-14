import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:flutter_dotenv/flutter_dotenv.dart';

class ApiProvider {
  String get baseUrl {
    return dotenv.env['API_BASE_URL'] ?? 'http://192.168.121.197:8000/api';
  }

  /// Fetch latest sensor data for a specific device slug.
  Future<Map<String, dynamic>?> fetchLatestSensorData(
      {String slug = 'cybernova-s400-primary'}) async {
    try {
      final uri = Uri.parse('$baseUrl/sensor-data/latest/$slug');
      final response = await http.get(uri).timeout(const Duration(seconds: 8));
      if (response.statusCode == 200) {
        return json.decode(response.body);
      }
    } catch (_) {}
    return null;
  }

  /// Fetch all registered devices for the dropdown selector.
  Future<List<Map<String, dynamic>>> fetchDevices() async {
    try {
      final uri = Uri.parse('$baseUrl/devices');
      final response = await http.get(uri).timeout(const Duration(seconds: 8));
      if (response.statusCode == 200) {
        final body = json.decode(response.body);
        if (body['status'] == 'success') {
          return List<Map<String, dynamic>>.from(body['data']);
        }
      }
    } catch (_) {}
    return [];
  }

  /// Fetch stats for a device (avg, min, max)
  Future<Map<String, dynamic>?> fetchSensorStats(String slug) async {
    try {
      final uri = Uri.parse('$baseUrl/sensor-data/stats/$slug');
      final response = await http.get(uri).timeout(const Duration(seconds: 8));
      if (response.statusCode == 200) {
        return json.decode(response.body);
      }
    } catch (_) {}
    return null;
  }

  /// Fetch water level history for a device
  Future<Map<String, dynamic>?> fetchHistory({
    required String slug,
    String range = 'daily',
  }) async {
    try {
      final uri = Uri.parse('$baseUrl/water-level/history?device_slug=$slug&range=$range');
      final response = await http.get(uri).timeout(const Duration(seconds: 10));
      if (response.statusCode == 200) {
        return json.decode(response.body);
      }
    } catch (_) {}
    return null;
  }

  /// Fetch active alerts
  Future<List<dynamic>> fetchActiveAlerts({String? slug}) async {
    try {
      String url = '$baseUrl/notifications/active-alert';
      if (slug != null) url += '?device_slug=$slug';
      final uri = Uri.parse(url);
      final response = await http.get(uri).timeout(const Duration(seconds: 8));
      if (response.statusCode == 200) {
        final body = json.decode(response.body);
        if (body['status'] == 'success') {
          return body['alerts'] ?? [];
        }
      }
    } catch (_) {}
    return [];
  }
}
