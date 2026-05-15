import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:flutter_dotenv/flutter_dotenv.dart';

class WeatherProvider {
  // API Key WeatherAPI
  String get apiKey => dotenv.env['WEATHER_API_KEY'] ?? '9a77c5d39a304c8ba5670449261205';

  /// Fetches current weather from Unified Laravel Backend
  Future<Map<String, dynamic>?> fetchWeather({
    required double latitude,
    required double longitude,
  }) async {
    try {
      final baseUrl = dotenv.env['API_BASE_URL'] ?? 'http://103.172.205.35/api';
      final uri = Uri.parse('$baseUrl/weather?lat=$latitude&lon=$longitude');

      final response = await http.get(uri).timeout(const Duration(seconds: 10));

      if (response.statusCode == 200) {
        final body = json.decode(response.body);
        final condition = body['condition'] as Map<String, dynamic>;
        final code = condition['code'] as int;

        return {
          'temperature': (body['temp_c'] as num).toDouble(),
          'windspeed': (body['wind_kph'] as num).toDouble(),
          'humidity': body['humidity'],
          'weathercode': code,
          'description': condition['text'],
          'icon': 'https:${condition['icon']}', // Gunakan URL ikon asli
          'locationName': body['location'],
          'region': body['region'],
        };
      }
    } catch (e) {
      print('Unified Weather Error: $e');
    }
    return null;
  }

}
