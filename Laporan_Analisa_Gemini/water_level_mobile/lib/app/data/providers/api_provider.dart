import 'dart:convert';
import 'package:http/http.dart' as http;

class ApiProvider {
  // Use 10.0.2.2 for Android emulator to access localhost, or replace with your actual IP
  static const String baseUrl = 'http://127.0.0.1:8000/api';

  Future<Map<String, dynamic>?> fetchLatestSensorData() async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/sensor-data/latest'));
      if (response.statusCode == 200) {
        return json.decode(response.body);
      }
    } catch (e) {
      print('Error fetching data: $e');
    }
    return null;
  }
}
