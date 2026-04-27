import 'dart:async';
import 'package:get/get.dart';
import '../../../data/providers/api_provider.dart';

class HomeController extends GetxController {
  final ApiProvider _apiProvider = ApiProvider();
  
  var isLoading = true.obs;
  var distance = 0.0.obs;
  var waterLevel = 0.0.obs;
  var validCount = 0.obs;
  var lastUpdated = ''.obs;

  Timer? _timer;

  @override
  void onInit() {
    super.onInit();
    fetchData();
    // Auto-refresh every 5 seconds
    _timer = Timer.periodic(const Duration(seconds: 5), (_) => fetchData());
  }

  @override
  void onClose() {
    _timer?.cancel();
    super.onClose();
  }

  Future<void> fetchData() async {
    final response = await _apiProvider.fetchLatestSensorData();
    if (response != null && response['status'] == 'success') {
      final data = response['data'];
      distance.value = (data['distance'] != null) ? double.parse(data['distance'].toString()) : 0.0;
      validCount.value = data['valid_count'] ?? 0;
      lastUpdated.value = data['created_at'] ?? '';
      
      // Calculate water level (assuming tank height 200cm, distance is from top)
      // Example visualization calculation, can be adjusted
      waterLevel.value = (200.0 - distance.value).clamp(0.0, 200.0);
    }
    isLoading.value = false;
  }
}
