import 'dart:convert';
import 'package:http/http.dart' as http;

import 'package:get_storage/get_storage.dart';

class ApiProvider {
  final _storage = GetStorage();
  
  String get baseUrl {
    final ip = _storage.read('server_ip') ?? '127.0.0.1';
    return 'http://$ip:8000/api';
  }

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
