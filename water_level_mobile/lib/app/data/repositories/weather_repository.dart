import '../providers/base_provider.dart';

class WeatherRepository extends BaseProvider {
  /// Fetches current weather from Unified Laravel Backend
  Future<Map<String, dynamic>?> fetchWeather({
    required double latitude,
    required double longitude,
  }) async {
    try {
      final response = await dio.get('weather', queryParameters: {
        'lat': latitude,
        'lon': longitude,
        'lang': 'id', // Request Indonesian from server/API
      });

      if (response.statusCode == 200) {
        final body = response.data;
        final condition = body['condition'] as Map<String, dynamic>;
        final code = condition['code'] as int;

        return {
          'temperature': (body['temp_c'] as num).toDouble(),
          'windspeed': (body['wind_kph'] as num).toDouble(),
          'humidity': body['humidity'],
          'weathercode': code,
          'description': _translateDescription(condition['text'] ?? ''),
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

  String _translateDescription(String text) {
    final Map<String, String> translations = {
      'Sunny': 'Cerah',
      'Clear': 'Cerah',
      'Partly cloudy': 'Berawan Sebagian',
      'Cloudy': 'Berawan',
      'Overcast': 'Mendung',
      'Mist': 'Berkabut',
      'Patchy rain possible': 'Kemungkinan Hujan',
      'Patchy snow possible': 'Kemungkinan Salju',
      'Patchy sleet possible': 'Kemungkinan Hujan Es',
      'Patchy freezing drizzle possible': 'Kemungkinan Gerimis Beku',
      'Thundery outbreaks possible': 'Kemungkinan Badai Guntur',
      'Blowing snow': 'Salju Beriup',
      'Blizzard': 'Badai Salju',
      'Fog': 'Kabut',
      'Freezing fog': 'Kabut Beku',
      'Patchy light drizzle': 'Gerimis Ringan Lokal',
      'Light drizzle': 'Gerimis Ringan',
      'Freezing drizzle': 'Gerimis Beku',
      'Heavy freezing drizzle': 'Gerimis Beku Lebat',
      'Patchy light rain': 'Hujan Ringan Lokal',
      'Light rain': 'Hujan Ringan',
      'Moderate rain at times': 'Hujan Sedang Terkadang',
      'Moderate rain': 'Hujan Sedang',
      'Heavy rain at times': 'Hujan Lebat Terkadang',
      'Heavy rain': 'Hujan Lebat',
      'Light freezing rain': 'Hujan Beku Ringan',
      'Moderate or heavy freezing rain': 'Hujan Beku Sedang atau Lebat',
      'Light sleet': 'Hujan Es Ringan',
      'Moderate or heavy sleet': 'Hujan Es Sedang atau Lebat',
      'Patchy light snow': 'Salju Ringan Lokal',
      'Light snow': 'Salju Ringan',
      'Patchy moderate snow': 'Salju Sedang Lokal',
      'Moderate snow': 'Salju Sedang',
      'Patchy heavy snow': 'Salju Lebat Lokal',
      'Heavy snow': 'Salju Lebat',
      'Ice pellets': 'Pelet Es',
      'Light rain shower': 'Hujan Gerimis',
      'Moderate or heavy rain shower': 'Hujan Deras',
      'Torrential rain shower': 'Hujan Sangat Deras',
      'Light sleet showers': 'Hujan Es Ringan',
      'Moderate or heavy sleet showers': 'Hujan Es Sedang atau Lebat',
      'Light snow showers': 'Hujan Salju Ringan',
      'Moderate or heavy snow showers': 'Hujan Salju Sedang atau Lebat',
      'Light showers of ice pellets': 'Hujan Pelet Es Ringan',
      'Moderate or heavy showers of ice pellets': 'Hujan Pelet Es Sedang atau Lebat',
      'Patchy light rain with thunder': 'Hujan Ringan disertai Petir',
      'Moderate or heavy rain with thunder': 'Hujan Sedang atau Lebat disertai Petir',
      'Patchy light snow with thunder': 'Salju Ringan disertai Petir',
      'Moderate or heavy snow with thunder': 'Salju Sedang atau Lebat disertai Petir',
      'Patchy rain nearby': 'Hujan Ringan di Sekitar',
    };

    // Case insensitive matching
    final String lowercaseText = text.trim();
    for (var entry in translations.entries) {
      if (entry.key.toLowerCase() == lowercaseText.toLowerCase()) {
        return entry.value;
      }
    }

    return text; // Fallback to original text if no translation found
  }
}
