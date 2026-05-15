import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:flutter_dotenv/flutter_dotenv.dart';

class ApiProvider {
  String get baseUrl {
    return dotenv.env['API_BASE_URL'] ?? 'http://103.172.205.35/api';
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
    String? startDate,
    String? endDate,
  }) async {
    try {
      String url = '$baseUrl/water-level/history?device_slug=$slug&range=$range';
      if (startDate != null) url += '&start_date=$startDate';
      if (endDate != null) url += '&end_date=$endDate';
      
      final uri = Uri.parse(url);
      print('Fetching History: $uri');
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

  // ── Authentication Methods ──────────────────────────────────────────

  /// Login to account
  Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final uri = Uri.parse('$baseUrl/login');
      final response = await http.post(
        uri,
        headers: {'Content-Type': 'application/json'},
        body: json.encode({'email': email, 'password': password}),
      ).timeout(const Duration(seconds: 10));
      
      return {
        'statusCode': response.statusCode,
        'data': json.decode(response.body)
      };
    } catch (e) {
      return {'statusCode': 500, 'data': {'message': 'Terjadi kesalahan koneksi.'}};
    }
  }

  /// Register new account
  Future<Map<String, dynamic>> register(Map<String, dynamic> userData) async {
    try {
      final uri = Uri.parse('$baseUrl/register');
      final response = await http.post(
        uri,
        headers: {'Content-Type': 'application/json'},
        body: json.encode(userData),
      ).timeout(const Duration(seconds: 15));
      
      return {
        'statusCode': response.statusCode,
        'data': json.decode(response.body)
      };
    } catch (e) {
      return {'statusCode': 500, 'data': {'message': 'Terjadi kesalahan koneksi.'}};
    }
  }

  /// Request password reset link
  Future<Map<String, dynamic>> forgotPassword(String email, String method) async {
    try {
      final uri = Uri.parse('$baseUrl/forgot-password');
      final response = await http.post(
        uri,
        headers: {'Content-Type': 'application/json'},
        body: json.encode({'email': email, 'method': method}),
      ).timeout(const Duration(seconds: 10));
      
      return {
        'statusCode': response.statusCode,
        'data': json.decode(response.body)
      };
    } catch (e) {
      return {'statusCode': 500, 'data': {'message': 'Terjadi kesalahan koneksi.'}};
    }
  }

  /// Reset password with token
  Future<Map<String, dynamic>> resetPassword(Map<String, dynamic> resetData) async {
    try {
      final uri = Uri.parse('$baseUrl/reset-password');
      final response = await http.post(
        uri,
        headers: {'Content-Type': 'application/json'},
        body: json.encode(resetData),
      ).timeout(const Duration(seconds: 10));
      
      return {
        'statusCode': response.statusCode,
        'data': json.decode(response.body)
      };
    } catch (e) {
      return {'statusCode': 500, 'data': {'message': 'Terjadi kesalahan koneksi.'}};
    }
  }

  Future<Map<String, dynamic>> googleLogin(Map<String, dynamic> data) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/google-login'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode(data),
      );
      return {
        'statusCode': response.statusCode,
        'data': json.decode(response.body),
      };
    } catch (e) {
      return {'statusCode': 500, 'data': {'message': e.toString()}};
    }
  }

  Future<Map<String, dynamic>> completeProfile(Map<String, dynamic> data, String token) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/complete-profile'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: jsonEncode(data),
      );
      return {
        'statusCode': response.statusCode,
        'data': json.decode(response.body),
      };
    } catch (e) {
      return {'statusCode': 500, 'data': {'message': e.toString()}};
    }
  }

  Future<Map<String, dynamic>> updateProfile(Map<String, dynamic> data, String token) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/update-profile'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: jsonEncode(data),
      );
      return {
        'statusCode': response.statusCode,
        'data': json.decode(response.body),
      };
    } catch (e) {
      return {'statusCode': 500, 'data': {'message': e.toString()}};
    }
  }
}
