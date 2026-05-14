import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:flutter_dotenv/flutter_dotenv.dart';

class WeatherProvider {
  // API Key WeatherAPI
  String get apiKey => dotenv.env['WEATHER_API_KEY'] ?? '9a77c5d39a304c8ba5670449261205';

  /// Fetches current weather from WeatherAPI.com
  ///
  /// Returns a map with: temperature, weatherCode, windspeed, description, icon
  Future<Map<String, dynamic>?> fetchWeather({
    required double latitude,
    required double longitude,
  }) async {

    try {
      final uri = Uri.parse(
        'https://api.weatherapi.com/v1/current.json'
        '?key=$apiKey'
        '&q=$latitude,$longitude'
        '&lang=id' // Meminta respons teks dalam bahasa Indonesia
        '&aqi=no',
      );

      final response = await http.get(uri).timeout(const Duration(seconds: 10));

      if (response.statusCode == 200) {
        final body = json.decode(response.body);
        final current = body['current'] as Map<String, dynamic>;
        final condition = current['condition'] as Map<String, dynamic>;
        final code = condition['code'] as int;

        final location = body['location'] as Map<String, dynamic>;

        return {
          'temperature': current['temp_c'],
          'windspeed': current['wind_kph'],
          'humidity': current['humidity'],
          'weathercode': code,
          'description':
              condition['text'], // Deskripsi langsung dari WeatherAPI
          'icon': _weatherIcon(code),
          'locationName': location['name'],
          'region': location['region'],
        };
      } else {
        print('Gagal mengambil cuaca: ${response.body}');
      }
    } catch (e) {
      print('WeatherAPI error: $e');
    }
    return null;
  }

  // Memetakan kode kondisi dari WeatherAPI ke Emoji agar UI tetap cantik
  String _weatherIcon(int code) {
    switch (code) {
      case 1000:
        return '☀️'; // Cerah
      case 1003:
        return '⛅'; // Sebagian Berawan
      case 1006:
      case 1009:
        return '☁️'; // Berawan / Mendung
      case 1030:
      case 1135:
      case 1148:
        return '🌫️'; // Kabut
      case 1063:
      case 1180:
      case 1183:
      case 1240:
        return '🌦️'; // Hujan Ringan / Gerimis
      case 1186:
      case 1189:
      case 1192:
      case 1195:
      case 1243:
      case 1246:
        return '🌧️'; // Hujan Sedang / Lebat
      case 1087:
      case 1273:
      case 1276:
        return '⛈️'; // Hujan Badai Petir
      default:
        return '🌡️'; // Default
    }
  }
}
