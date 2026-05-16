import '../models/sensor_data_model.dart';
import '../providers/base_provider.dart';

class SensorRepository extends BaseProvider {
  Future<SensorDataModel?> getLatestData(String slug) async {
    try {
      final response = await dio.get('sensor-data/latest/$slug');
      if (response.statusCode == 200) {
        return SensorDataModel.fromJson(response.data);
      }
      return null;
    } catch (e) {
      return null;
    }
  }

  Future<Map<String, dynamic>?> getStats(String slug) async {
    try {
      final response = await dio.get('sensor-data/stats/$slug');
      if (response.statusCode == 200) {
        return response.data['data'];
      }
      return null;
    } catch (e) {
      return null;
    }
  }

  Future<Map<String, dynamic>?> getHistory({
    required String slug,
    String range = 'daily',
    String? startDate,
    String? endDate,
  }) async {
    try {
      final response = await dio.get('water-level/history', queryParameters: {
        'device_slug': slug,
        'range': range,
        if (startDate != null) 'start_date': startDate,
        if (endDate != null) 'end_date': endDate,
      });
      if (response.statusCode == 200) {
        return response.data;
      }
      return null;
    } catch (e) {
      return null;
    }
  }
}
