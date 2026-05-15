import 'package:get/get.dart';
import 'package:latlong2/latlong.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:get_storage/get_storage.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../data/providers/api_provider.dart';

class DeviceMapController extends GetxController {
  final ApiProvider _apiProvider = ApiProvider();
  final _storage = GetStorage();
  
  var devices = <Map<String, dynamic>>[].obs;
  var isLoading = true.obs;
  var searchQuery = ''.obs;
  var isSearchFocused = false.obs;
  
  // Filtered devices based on search query
  List<Map<String, dynamic>> get filteredDevices {
    if (searchQuery.value.isEmpty) return devices;
    return devices.where((device) {
      final name = (device['name'] ?? '').toString().toLowerCase();
      final location = (device['location'] ?? '').toString().toLowerCase();
      return name.contains(searchQuery.value.toLowerCase()) || 
             location.contains(searchQuery.value.toLowerCase());
    }).toList();
  }
  
  bool get shouldShowSearchResults => isSearchFocused.value;
  
  // Map Layer Selection (0: Standard, 1: Satellite, 2: Dark)
  var currentMapLayer = 0.obs;
  
  // Selected device for info overlay
  var selectedDevice = Rxn<Map<String, dynamic>>();
  
  final MapController mapController = MapController();

  @override
  void onInit() {
    super.onInit();
    fetchDevices();
  }

  Future<void> fetchDevices() async {
    isLoading.value = true;
    final list = await _apiProvider.fetchDevices();
    
    devices.value = list;
    isLoading.value = false;
    
    if (list.isNotEmpty) {
      final savedSlug = _storage.read<String>('selected_device_slug');
      if (savedSlug != null) {
        final savedDevice = list.firstWhere(
          (d) => d['slug'] == savedSlug,
          orElse: () => list.first,
        );
        selectDevice(savedDevice);
      } else {
        selectDevice(list.first);
      }
    }
  }

  Future<void> selectDevice(Map<String, dynamic> device, {bool silent = false}) async {
    selectedDevice.value = device;
    mapController.move(
      LatLng(
        double.tryParse(device['latitude']?.toString() ?? '0') ?? 0.0,
        double.tryParse(device['longitude']?.toString() ?? '0') ?? 0.0,
      ),
      15.0,
    );
    
    // Refresh data silently to get latest TMA
    if (!silent) {
      await refreshData();
    }
  }

  Future<void> refreshData() async {
    final list = await _apiProvider.fetchDevices();
    if (list.isNotEmpty) {
      devices.value = list;
      // Update selected device with fresh data
      if (selectedDevice.value != null) {
        final fresh = list.firstWhere(
          (d) => d['slug'] == selectedDevice.value!['slug'],
          orElse: () => selectedDevice.value!,
        );
        selectedDevice.value = fresh;
      }
    }
  }

  void nextDevice() {
    if (filteredDevices.isEmpty) return;
    if (selectedDevice.value == null) {
      selectDevice(filteredDevices.first);
      return;
    }
    
    final currentIndex = filteredDevices.indexWhere((d) => d['slug'] == selectedDevice.value!['slug']);
    if (currentIndex == -1 || currentIndex == filteredDevices.length - 1) {
      selectDevice(filteredDevices.first); // Loop back to start
    } else {
      selectDevice(filteredDevices[currentIndex + 1]);
    }
  }

  void previousDevice() {
    if (filteredDevices.isEmpty) return;
    if (selectedDevice.value == null) {
      selectDevice(filteredDevices.last);
      return;
    }
    
    final currentIndex = filteredDevices.indexWhere((d) => d['slug'] == selectedDevice.value!['slug']);
    if (currentIndex == -1 || currentIndex == 0) {
      selectDevice(filteredDevices.last); // Loop back to end
    } else {
      selectDevice(filteredDevices[currentIndex - 1]);
    }
  }

  void toggleMapLayer() {
    currentMapLayer.value = (currentMapLayer.value + 1) % 3;
  }

  Future<void> openInGoogleMaps() async {
    final device = selectedDevice.value;
    if (device == null) return;

    final lat = device['latitude'];
    final lng = device['longitude'];
    
    final Uri url = Uri.parse('https://www.google.com/maps/search/?api=1&query=$lat,$lng');
    try {
      if (await canLaunchUrl(url)) {
        await launchUrl(url, mode: LaunchMode.externalApplication);
      } else {
        print("Could not launch maps");
      }
    } catch (e) {
      print("Could not launch maps: $e");
    }
  }
}
