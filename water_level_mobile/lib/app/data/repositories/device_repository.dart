import '../models/device_model.dart';
import '../providers/base_provider.dart';

class DeviceRepository extends BaseProvider {
  Future<List<DeviceModel>> getDevices() async {
    try {
      final response = await dio.get('devices');
      if (response.data['status'] == 'success') {
        final List list = response.data['data'];
        return list.map((e) => DeviceModel.fromJson(e)).toList();
      }
      return [];
    } catch (e) {
      return [];
    }
  }

  Future<DeviceModel?> getLatestData(String slug) async {
    try {
      final response = await dio.get('sensor-data/latest/$slug');
      if (response.data['status'] == 'success') {
        return DeviceModel.fromJson(response.data['data']);
      }
      return null;
    } catch (e) {
      return null;
    }
  }
}
