import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:latlong2/latlong.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:get_storage/get_storage.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:geolocator/geolocator.dart';
import '../../../data/repositories/device_repository.dart';
import '../../../data/repositories/weather_repository.dart';
import '../../../data/models/device_model.dart';

class DeviceMapController extends GetxController {
  final DeviceRepository _deviceRepo = Get.find<DeviceRepository>();
  final WeatherRepository _weatherRepo = Get.find<WeatherRepository>();
  final _storage = GetStorage();
  
  var devices = <DeviceModel>[].obs;
  var isLoading = true.obs;
  var searchQuery = ''.obs;
  var isSearchFocused = false.obs;
  
  // Filtered devices based on search query
  List<DeviceModel> get filteredDevices {
    if (searchQuery.value.isEmpty) return devices;
    return devices.where((device) {
      final name = device.name.toLowerCase();
      final location = (device.location ?? '').toLowerCase();
      return name.contains(searchQuery.value.toLowerCase()) || 
             location.contains(searchQuery.value.toLowerCase());
    }).toList();
  }
  
  bool get shouldShowSearchResults => isSearchFocused.value;
  
  // Map Layer Selection (0: Standard, 1: Satellite, 2: Dark)
  var currentMapLayer = 0.obs;
  
  // Selected device for info overlay
  var selectedDevice = Rxn<DeviceModel>();
  
  // User Locations
  var gpsLocation = Rxn<LatLng>();
  var profileLocation = Rxn<LatLng>();
  var showUserLocation = true.obs;

  // Weather for selected device
  var weatherIcon = ''.obs;
  var weatherTemp = 0.0.obs;
  var weatherDesc = ''.obs;
  var weatherWindspeed = 0.0.obs;
  var weatherHumidity = 0.obs;
  var weatherLoading = false.obs;
  
  final MapController mapController = MapController();

  @override
  void onInit() {
    super.onInit();
    fetchDevices();
    _determineUserPosition();
  }

  Future<void> _determineUserPosition() async {
    try {
      bool serviceEnabled;
      LocationPermission permission;

      serviceEnabled = await Geolocator.isLocationServiceEnabled();
      if (!serviceEnabled) return;

      permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
      }
      
      if (permission == LocationPermission.always || permission == LocationPermission.whileInUse) {
        final position = await Geolocator.getCurrentPosition();
        gpsLocation.value = LatLng(position.latitude, position.longitude);
      }
    } catch (e) {
      debugPrint('GPS Error: $e');
    } finally {
      _loadProfileLocation();
    }
  }

  void _loadProfileLocation() {
    try {
      final userData = _storage.read('user');
      debugPrint('MapController: Loading profile location from storage: $userData');
      if (userData != null) {
        final lat = double.tryParse(userData['latitude']?.toString() ?? '');
        final lng = double.tryParse(userData['longitude']?.toString() ?? '');
        if (lat != null && lng != null) {
          profileLocation.value = LatLng(lat, lng);
          debugPrint('MapController: Profile location loaded: $lat, $lng');
        } else {
          debugPrint('MapController: Profile location lat/lng are null in storage');
        }
      } else {
        debugPrint('MapController: No user data found in storage');
      }
    } catch (e) {
      debugPrint('Error loading profile location: $e');
    }
  }

  String? getGpsDistance(DeviceModel device) => _calculateDistance(gpsLocation.value, device);
  String? getProfileDistance(DeviceModel device) => _calculateDistance(profileLocation.value, device);

  String? _calculateDistance(LatLng? origin, DeviceModel device) {
    if (origin == null || device.latitude == null || device.longitude == null) {
      return null;
    }
    
    final double distance = Geolocator.distanceBetween(
      origin.latitude,
      origin.longitude,
      device.latitude!,
      device.longitude!,
    );
    
    if (distance < 1000) {
      return '${distance.toStringAsFixed(0)}m';
    }
    return '${(distance / 1000).toStringAsFixed(1)}km';
  }

  Future<void> fetchDevices() async {
    isLoading.value = true;
    final list = await _deviceRepo.getDevices();
    
    devices.value = list;
    isLoading.value = false;
    
    if (list.isNotEmpty) {
      final savedSlug = _storage.read<String>('selected_device_slug');
      if (savedSlug != null) {
        final savedDevice = list.firstWhere(
          (d) => d.slug == savedSlug,
          orElse: () => list.first,
        );
        selectDevice(savedDevice);
      } else {
        selectDevice(list.first);
      }
    }
  }

  Future<void> selectDevice(DeviceModel device, {bool silent = false}) async {
    selectedDevice.value = device;
    mapController.move(
      LatLng(
        device.latitude ?? 0.0,
        device.longitude ?? 0.0,
      ),
      15.0,
    );
    _fetchWeatherForDevice(device);
    
    // Refresh data silently to get latest TMA
    if (!silent) {
      await refreshData();
    }
  }

  Future<void> _fetchWeatherForDevice(DeviceModel device) async {
    if (device.latitude == null || device.longitude == null) return;
    
    weatherLoading.value = true;
    try {
      final data = await _weatherRepo.fetchWeather(
        latitude: device.latitude!,
        longitude: device.longitude!,
      );
      
      if (data != null) {
        weatherTemp.value = data['temperature'];
        weatherDesc.value = data['description'];
        weatherIcon.value = data['icon'];
        weatherWindspeed.value = data['windspeed'];
        weatherHumidity.value = data['humidity'];
      }
    } catch (e) {
      debugPrint('Error fetching map weather: $e');
    }
    weatherLoading.value = false;
  }

  Future<void> refreshData() async {
    final list = await _deviceRepo.getDevices();
    if (list.isNotEmpty) {
      devices.value = list;
      // Update selected device with fresh data
      if (selectedDevice.value != null) {
        final fresh = list.firstWhere(
          (d) => d.slug == selectedDevice.value!.slug,
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
    
    final currentIndex = filteredDevices.indexWhere((d) => d.slug == selectedDevice.value!.slug);
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
    
    final currentIndex = filteredDevices.indexWhere((d) => d.slug == selectedDevice.value!.slug);
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

    final lat = device.latitude;
    final lng = device.longitude;
    
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
