import 'dart:async';
import 'dart:ui' as ui;
import 'package:flutter/services.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:vibration/vibration.dart';
import 'package:flutter_ringtone_player/flutter_ringtone_player.dart';
import 'package:audioplayers/audioplayers.dart';
import 'package:intl/intl.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:geolocator/geolocator.dart';
import 'package:geocoding/geocoding.dart';
import 'package:connectivity_plus/connectivity_plus.dart';
import '../../../data/providers/api_provider.dart';
import '../../../data/providers/weather_provider.dart';
import '../../../services/notification_service.dart';

class HomeController extends GetxController {
  final ApiProvider _apiProvider = ApiProvider();
  final WeatherProvider _weatherProvider = WeatherProvider();
  final _storage = GetStorage();
  final AudioPlayer _audioPlayer = AudioPlayer();

  // ── Loading & Network ───────────────────────────────────────────
  var isLoading = true.obs;
  var isRefreshing = false.obs;
  var hasInternet = true.obs;
  var pullDistance = 0.0.obs;
  var isManualRefreshing = false.obs;
  var scrollOffset = 0.0.obs;

  // ── Device list (dropdown) ───────────────────────────────────────
  var devices = <Map<String, dynamic>>[].obs;
  var selectedDeviceSlug = ''.obs;
  var selectedDeviceName = 'Pilih Node...'.obs;
  var selectedDeviceLocation = ''.obs;
  var selectedDeviceLat = 0.0.obs;
  var selectedDeviceLng = 0.0.obs;

  // ── User Location & Weather ──────────────────────────────────────
  var userLat = 0.0.obs;
  var userLng = 0.0.obs;
  var userAddress = 'Lokasi Saya'.obs;

  // ── Sensor data ──────────────────────────────────────────────────
  var distance = 0.0.obs;
  var waterLevel = 0.0.obs;
  var validCount = 0.obs;
  var lastUpdated = ''.obs;
  var isOnline = false.obs;
  
  var flowVelocity = 0.0.obs;
  var flowTrend = 0.obs;
  var sparklineData = <double>[].obs;
  var _previousWaterLevel = 0.0;
  
  // ── Sensor Stats (24h) ──────────────────────────────────────────
  var avgDistance = 0.0.obs;
  var minDistance = 0.0.obs;
  var maxDistance = 0.0.obs;
  
  var avgWaterLevel = 0.0.obs;
  var minWaterLevel = 0.0.obs;
  var maxWaterLevel = 0.0.obs;

  // ── Status siaga ─────────────────────────────────────────────────
  var statusSiaga = 'OFFLINE'.obs;
  var statusColor = 0xFF64748B.obs; // slate/gray
  var etaOverflow = '> 2 Jam'.obs;
  var distanceToGround = 0.0.obs;
  String _lastNotifiedStatus = '';

  // ── Calibration config ───────────────────────────────────────────
  var elevationMdpl = 14.0.obs;
  var sensorToBank = 100.0.obs;
  var riverDepth = 100.0.obs;

  // ── Weather ──────────────────────────────────────────────────────
  var weatherLoading = false.obs;
  var weatherTemp = 0.0.obs;
  var weatherDesc = ''.obs;
  var weatherIcon = '🌡️'.obs;
  var weatherCode = 0.obs;
  var weatherWindspeed = 0.0.obs;
  var weatherHumidity = 0.obs;
  var weatherLocationName = '...'.obs;

  // ── Alarm ────────────────────────────────────────────────────────
  var isAlarmMuted = false.obs;
  // ── Animation ──────────────────────────────────────────────────
  var wavePhase = 0.0.obs;
  final cityImg = Rxn<ui.Image>();
  Timer? _animationTimer;
  Timer? _snoozeTimer;
  Timer? _dataTimer;
  Timer? _weatherTimer;
  Timer? _statusDebounceTimer;
  String _pendingStatus = '';

  @override
  void onInit() {
    super.onInit();
    _loadCityImg();
    _loadSavedDevice();
    fetchDevices();
    _initUserLocation();
    // Refresh sensor data every 5 sec
    _dataTimer = Timer.periodic(const Duration(seconds: 5), (_) {
      if (selectedDeviceSlug.value.isNotEmpty) fetchSensorData();
    });
    // Animation Ticker (approx 60fps)
    _animationTimer = Timer.periodic(const Duration(milliseconds: 16), (_) {
      wavePhase.value += 0.05;
    });

    // Refresh weather every 10 min
    _weatherTimer = Timer.periodic(const Duration(minutes: 10), (_) {
      _fetchWeather();
    });

    // Monitor Internet Connection
    Connectivity().onConnectivityChanged.listen((List<ConnectivityResult> results) {
      if (results.contains(ConnectivityResult.none)) {
        hasInternet.value = false;
        isOnline.value = false;
      } else {
        hasInternet.value = true;
        // Auto retry fetch if devices are empty
        if (devices.isEmpty) fetchDevices();
      }
    });
  }

  @override
  void onClose() {
    _animationTimer?.cancel();
    _dataTimer?.cancel();
    _weatherTimer?.cancel();
    _snoozeTimer?.cancel();
    _statusDebounceTimer?.cancel();
    _audioPlayer.dispose();
    super.onClose();
  }

  Future<void> _loadCityImg() async {
    try {
      final ByteData data = await rootBundle.load('assets/images/city.png');
      final ui.Codec codec =
          await ui.instantiateImageCodec(data.buffer.asUint8List());
      final ui.FrameInfo fi = await codec.getNextFrame();
      cityImg.value = fi.image;
    } catch (e) {
      debugPrint('Error fetching sensor data: $e');
    }
  }

  Future<void> fetchSensorStats() async {
    if (selectedDeviceSlug.value.isEmpty) return;
    try {
      final response = await _apiProvider.fetchSensorStats(selectedDeviceSlug.value);
      if (response != null && response['status'] == 'success') {
        final data = response['data'];
        avgDistance.value = (data['avg_distance'] as num).toDouble();
        minDistance.value = (data['min_distance'] as num).toDouble();
        maxDistance.value = (data['max_distance'] as num).toDouble();
        
        // Convert to water levels
        avgWaterLevel.value = _calculateLevel(avgDistance.value);
        minWaterLevel.value = _calculateLevel(maxDistance.value); 
        maxWaterLevel.value = _calculateLevel(minDistance.value);
      }
    } catch (e) {
      debugPrint('Error fetching stats: $e');
    }
  }
  
  double _calculateLevel(double dist) {
    final bankLevelMdpl = elevationMdpl.value - (sensorToBank.value / 100.0);
    final riverBedMdpl = bankLevelMdpl - (riverDepth.value / 100.0);
    
    // actual_water_height = riverDepth - (dist - sensorToBank)
    final distToWaterFromBank = dist - sensorToBank.value;
    final actualHeightCm = riverDepth.value - distToWaterFromBank;
    
    return riverBedMdpl + (actualHeightCm / 100.0);
  }

  // ── Device management ────────────────────────────────────────────

  void _loadSavedDevice() {
    final savedSlug = _storage.read<String>('selected_device_slug');
    if (savedSlug != null && savedSlug.isNotEmpty) {
      selectedDeviceSlug.value = savedSlug;
    }
  }

  Future<void> fetchDevices() async {
    isLoading.value = true;
    final list = await _apiProvider.fetchDevices();
    if (list.isNotEmpty) {
      devices.value = list;
      // Auto-select saved or first device
      if (selectedDeviceSlug.value.isEmpty) {
        _selectDevice(list.first);
      } else {
        final saved = list.firstWhere(
          (d) => d['slug'] == selectedDeviceSlug.value,
          orElse: () => list.first,
        );
        _selectDevice(saved);
      }
    }
    isLoading.value = false;
  }

  void _selectDevice(Map<String, dynamic> device) {
    selectedDeviceSlug.value = device['slug'] ?? '';
    selectedDeviceName.value = device['name'] ?? 'Node';
    selectedDeviceLocation.value = device['location'] ?? '';
    selectedDeviceLat.value =
        double.tryParse(device['latitude']?.toString() ?? '0') ?? 0.0;
    selectedDeviceLng.value =
        double.tryParse(device['longitude']?.toString() ?? '0') ?? 0.0;
    _storage.write('selected_device_slug', selectedDeviceSlug.value);
    fetchSensorData();
    _fetchWeather();
  }

  void onDeviceSelected(Map<String, dynamic> device) {
    _selectDevice(device);
  }

  // ── Sensor data ──────────────────────────────────────────────────

  Future<void> fetchSensorData() async {
    if (selectedDeviceSlug.value.isEmpty) return;
    try {
      final response = await _apiProvider.fetchLatestSensorData(
        slug: selectedDeviceSlug.value,
      );
      if (response != null && response['status'] == 'success') {
        final data = response['data'];
        distance.value =
            double.tryParse(data['distance']?.toString() ?? '0') ?? 0.0;
        validCount.value = int.tryParse(data['valid_count']?.toString() ?? '0') ?? 0;
        
        final rawDate = data['created_at']?.toString() ?? '';
        if (rawDate.isNotEmpty) {
          try {
            final dt = DateTime.parse(rawDate).toLocal();
            lastUpdated.value = DateFormat('HH:mm:ss').format(dt);
          } catch (_) {
            lastUpdated.value = rawDate;
          }
        } else {
          lastUpdated.value = '-- : --';
        }
        
        // Fetch stats as well
        fetchSensorStats();

        // Heartbeat check (server time is UTC)
        if (data['created_at'] != null) {
          final lastSeen = DateTime.parse(data['created_at']);
          isOnline.value =
              DateTime.now().toUtc().difference(lastSeen).inSeconds <= 30;
        } else {
          isOnline.value = false;
        }

        // Update calibration config from API
        if (response['config'] != null) {
          elevationMdpl.value = double.tryParse(
                  response['config']['elevation_mdpl'].toString()) ??
              14.0;
          sensorToBank.value = double.tryParse(
                  response['config']['sensor_to_bank'].toString()) ??
              100.0;
          riverDepth.value =
              double.tryParse(response['config']['river_depth'].toString()) ??
                  100.0;
        }

        // Compute derived values
        waterLevel.value = elevationMdpl.value - (distance.value / 100.0);
        distanceToGround.value = sensorToBank.value - (distance.value);

        if (_previousWaterLevel > 0) {
          final delta = waterLevel.value - _previousWaterLevel;
          flowVelocity.value = delta.abs(); // in meters (consistent with web)
          if (delta > 0.005) {
            flowTrend.value = 1;
          } else if (delta < -0.005) {
            flowTrend.value = -1;
          } else {
            flowTrend.value = 0;
          }
        }
        _previousWaterLevel = waterLevel.value;

        // Sparkline update
        sparklineData.add(waterLevel.value);
        if (sparklineData.length > 40) {
          sparklineData.removeAt(0);
        }

        _updateStatusSiaga();
        _updateAiEta();
      } else {
        isOnline.value = false;
        _updateStatusSiaga();
      }
    } catch (_) {
      isOnline.value = false;
      _updateStatusSiaga();
    } finally {
      isLoading.value = false;
      isRefreshing.value = false;
    }
  }

  Future<void> onManualRefresh() async {
    isManualRefreshing.value = true;
    await fetchSensorData();
    await fetchDevices();
    isManualRefreshing.value = false;
    pullDistance.value = 0.0;
  }

  void manualRefresh() {
    onManualRefresh();
  }

  // ── Status logic ─────────────────────────────────────────────────

  void _updateStatusSiaga() {
    if (!isOnline.value) {
      statusSiaga.value = 'OFFLINE';
      statusColor.value = 0xFF64748B;
      return;
    }

    String newStatus = '';
    int newColor = 0xFF22C55E;

    if (distanceToGround.value >= 0) {
      newStatus = 'SIAGA 1';
      newColor = 0xFFEF4444;
    } else if (distanceToGround.value >= -20) {
      newStatus = 'SIAGA 2';
      newColor = 0xFFF59E0B;
    } else if (distanceToGround.value >= -50) {
      newStatus = 'SIAGA 3';
      newColor = 0xFF3B82F6;
    } else {
      newStatus = 'AMAN';
      newColor = 0xFF22C55E;
    }

    // Always update color for immediate UI feedback on the gauge
    statusColor.value = newColor;

    // If status returns to current confirmed status, cancel pending changes
    if (newStatus == statusSiaga.value) {
      _statusDebounceTimer?.cancel();
      _statusDebounceTimer = null;
      _pendingStatus = newStatus;
      return;
    }

    // If a new status is detected, start/reset the 10s stability timer
    if (newStatus != _pendingStatus) {
      _pendingStatus = newStatus;
      _statusDebounceTimer?.cancel();
      _statusDebounceTimer = Timer(const Duration(seconds: 10), () {
        statusSiaga.value = _pendingStatus;
        _triggerAlert();
        _statusDebounceTimer = null;
      });
    }
  }

  void _triggerAlert() async {
    if (statusSiaga.value == _lastNotifiedStatus) return;
    _lastNotifiedStatus = statusSiaga.value;

    isAlarmMuted.value = false;
    _snoozeTimer?.cancel();

    // Notification
    switch (statusSiaga.value) {
      case 'SIAGA 1':
        await NotificationService.notifySiaga1();
        _startSiaga1Alarm();
        break;
      case 'SIAGA 2':
        await NotificationService.notifySiaga2();
        _audioPlayer.play(UrlSource(
            'https://assets.mixkit.co/active_storage/sfx/2568/2568-preview.mp3'));
        Get.snackbar(
          '⚠️ SIAGA 2',
          'Tinggi air mendekati batas bantaran. Pantau situasi!',
          backgroundColor: const Color(0xFFF59E0B),
          colorText: Colors.white,
          duration: const Duration(seconds: 5),
        );
        break;
      case 'SIAGA 3':
        await NotificationService.notifySiaga3();
        if (!kIsWeb) FlutterRingtonePlayer().playNotification();
        Get.snackbar(
          '📡 SIAGA 3',
          'Air naik signifikan. Status dimonitor aktif.',
          backgroundColor: const Color(0xFF3B82F6),
          colorText: Colors.white,
          duration: const Duration(seconds: 4),
        );
        break;
      default:
        await NotificationService.notifyAman();
        _stopAllAlarms();
    }

    // Vibration
    if (!kIsWeb && await Vibration.hasVibrator()) {
      if (statusSiaga.value == 'SIAGA 1') {
        Vibration.vibrate(pattern: [500, 200, 500, 200, 1000]);
      } else if (statusSiaga.value == 'SIAGA 2') {
        Vibration.vibrate(pattern: [300, 200, 300]);
      } else if (statusSiaga.value == 'SIAGA 3') {
        Vibration.vibrate(duration: 400);
      }
    }
  }

  void _startSiaga1Alarm() {
    _audioPlayer.setReleaseMode(ReleaseMode.loop);
    _audioPlayer.play(UrlSource(
        'https://assets.mixkit.co/active_storage/sfx/950/950-preview.mp3'));
    _showEvacuationDialog();
  }

  void _showEvacuationDialog() {
    // Prevent modal stacking
    if (Get.isDialogOpen == true) return;

    Get.dialog(
      AlertDialog(
        backgroundColor: const Color(0xFF7F1D1D),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        title: Column(
          children: [
            const Icon(Icons.warning_amber_rounded,
                color: Colors.white, size: 64),
            const SizedBox(height: 16),
            Text(
              'PERINGATAN EVAKUASI',
              textAlign: TextAlign.center,
              style: GoogleFonts.inter(
                  fontWeight: FontWeight.w900, color: Colors.white),
            ),
          ],
        ),
        content: Text(
          'Air sungai telah mencapai ambang batas SIAGA 1 (BAHAYA). Segera tinggalkan lokasi dan cari tempat yang lebih tinggi!',
          textAlign: TextAlign.center,
          style: GoogleFonts.inter(color: Colors.white70),
        ),
        actions: [
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: () {
                _stopAllAlarms();
                Get.back();
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.white,
                foregroundColor: Colors.red.shade900,
                padding: const EdgeInsets.symmetric(vertical: 16),
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12)),
              ),
              child: const Text('SAYA SUDAH EVAKUASI',
                  style: TextStyle(fontWeight: FontWeight.bold)),
            ),
          ),
          const SizedBox(height: 8),
          SizedBox(
            width: double.infinity,
            child: TextButton(
              onPressed: () {
                _stopAllAlarms();
                Get.back();
                _startSnooze();
              },
              child: const Text('INGATKAN 5 MENIT LAGI',
                  style: TextStyle(color: Colors.white60)),
            ),
          ),
        ],
      ),
      barrierDismissible: false,
    );
  }

  void _startSnooze() {
    _snoozeTimer = Timer(const Duration(minutes: 5), () {
      if (statusSiaga.value == 'SIAGA 1') _startSiaga1Alarm();
    });
  }

  void _stopAllAlarms() {
    _audioPlayer.stop();
    if (!kIsWeb) {
      FlutterRingtonePlayer().stop();
      Vibration.cancel();
    }
  }

  // ── ETA Calculation ──────────────────────────────────────────────

  void _updateAiEta() {
    // distanceToGround: positive = flood, negative = safe distance (cm)
    if (distanceToGround.value >= 0) {
      etaOverflow.value = 'LUBER!';
    } else if (distanceToGround.value >= -20) {
      etaOverflow.value = '< 15 Mnt';
    } else if (distanceToGround.value >= -50) {
      etaOverflow.value = '< 1 Jam';
    } else {
      etaOverflow.value = '> 2 Jam';
    }
  }

  // ── Weather ──────────────────────────────────────────────────────

  Future<void> _fetchWeather() async {
    // Selalu gunakan koordinat perangkat IoT untuk cuaca
    final targetLat = selectedDeviceLat.value;
    final targetLng = selectedDeviceLng.value;

    if (targetLat == 0.0) {
      weatherLocationName.value = 'Lokasi tidak ditemukan';
      return;
    }
    
    weatherLocationName.value = selectedDeviceLocation.value;
    
    weatherLoading.value = true;
    final data = await _weatherProvider.fetchWeather(
      latitude: targetLat,
      longitude: targetLng,
    );
    if (data != null) {
      weatherTemp.value = (data['temperature'] as num).toDouble();
      weatherDesc.value = data['description'] as String;
      weatherIcon.value = data['icon'] as String;
      weatherCode.value = data['weathercode'] as int;
      weatherWindspeed.value = (data['windspeed'] as num).toDouble();
      weatherHumidity.value = (data['humidity'] as num).toInt();
      
      if (data['locationName'] != null) {
        final region = data['region'] != null ? ', ${data['region']}' : '';
        weatherLocationName.value = '${data['locationName']}$region';
      }
    }
    weatherLoading.value = false;
  }

  // ── User Location GPS Logic ──────────────────────────────────────

  Future<void> _initUserLocation() async {
    // 1. Load last known from storage
    final lastLat = _storage.read<double>('last_user_lat');
    final lastLng = _storage.read<double>('last_user_lng');

    if (lastLat != null && lastLng != null) {
      userLat.value = lastLat;
      userLng.value = lastLng;
    }

    // 2. Check if GPS service is enabled
    bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) {
      if (lastLat != null) {
        _showGpsDisabledDialog();
      } else {
        // If no last location, we must ask to enable GPS
        _showEnableGpsPrompt();
      }
      return;
    }

    // 3. Check/Request Permission
    LocationPermission permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.denied) return;
    }

    if (permission == LocationPermission.deniedForever) return;

    // 4. Get Position (Try last known first, then current)
    try {
      Position? lastKnown = await Geolocator.getLastKnownPosition();
      if (lastKnown != null) {
        _updateUserLocation(lastKnown.latitude, lastKnown.longitude);
      }

      Position position = await Geolocator.getCurrentPosition(
        locationSettings: const LocationSettings(
          accuracy: LocationAccuracy.high,
          timeLimit: Duration(seconds: 15),
        ),
      );
      _updateUserLocation(position.latitude, position.longitude);
    } catch (e) {
      debugPrint('Error getting location: $e');
      // If we couldn't get user location, ensure we at least fetch weather for the device
      _fetchWeather();
    }
  }

  void _updateUserLocation(double lat, double lng) async {
    userLat.value = lat;
    userLng.value = lng;
    _storage.write('last_user_lat', lat);
    _storage.write('last_user_lng', lng);
    
    await _getAddressFromCoords(lat, lng);
    _sortAndSelectClosestDevice(lat, lng);
  }

  void _sortAndSelectClosestDevice(double userLat, double userLng) {
    if (devices.isEmpty) return;
    
    final sortedList = List<Map<String, dynamic>>.from(devices);
    sortedList.sort((a, b) {
      final latA = double.tryParse(a['latitude'].toString()) ?? 0.0;
      final lngA = double.tryParse(a['longitude'].toString()) ?? 0.0;
      final latB = double.tryParse(b['latitude'].toString()) ?? 0.0;
      final lngB = double.tryParse(b['longitude'].toString()) ?? 0.0;
      
      final distA = Geolocator.distanceBetween(userLat, userLng, latA, lngA);
      final distB = Geolocator.distanceBetween(userLat, userLng, latB, lngB);
      return distA.compareTo(distB);
    });
    
    devices.value = sortedList;
    
    // Jika user belum pernah memilih device, auto select yang paling dekat
    if (_storage.read<String>('selected_device_slug') == null) {
      _selectDevice(sortedList.first);
    }
  }

  Future<void> _getAddressFromCoords(double lat, double lng) async {
    try {
      List<Placemark> placemarks = await placemarkFromCoordinates(lat, lng);
      if (placemarks.isNotEmpty) {
        final place = placemarks.first;
        userAddress.value = '${place.subLocality ?? place.locality ?? 'Lokasi Saya'}';
      }
    } catch (e) {
      debugPrint('Geocoding error: $e');
    }
  }

  void _showGpsDisabledDialog() {
    Get.dialog(
      AlertDialog(
        backgroundColor: const Color(0xFF1A1D27),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text('GPS Tidak Aktif',
            style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
        content: const Text(
          'GPS Anda dimatikan. Apakah Anda ingin menggunakan lokasi terakhir yang tersimpan atau menyalakan GPS untuk data cuaca yang lebih akurat?',
          style: TextStyle(color: Colors.white70),
        ),
        actions: [
          TextButton(
            onPressed: () {
              Get.back();
              _fetchWeather(); // Use last saved (already in userLat.value)
            },
            child: const Text('Gunakan Lokasi Terakhir',
                style: TextStyle(color: Colors.white60)),
          ),
          ElevatedButton(
            onPressed: () async {
              Get.back();
              await Geolocator.openLocationSettings();
            },
            style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF4F7EF8)),
            child: const Text('Nyalakan GPS',
                style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  void _showEnableGpsPrompt() {
    Get.snackbar(
      'GPS Diperlukan',
      'Nyalakan GPS untuk mendapatkan informasi cuaca di lokasi Anda.',
      mainButton: TextButton(
        onPressed: () => Geolocator.openLocationSettings(),
        child: const Text('SETTING', style: TextStyle(color: Colors.white)),
      ),
      backgroundColor: Colors.black87,
      colorText: Colors.white,
      snackPosition: SnackPosition.BOTTOM,
    );
  }
}
