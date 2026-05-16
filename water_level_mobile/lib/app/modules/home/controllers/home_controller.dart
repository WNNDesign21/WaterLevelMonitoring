import 'dart:async';
import 'dart:ui' as ui;
import 'package:flutter/services.dart';
import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:water_level_mobile/app/core/utils/app_snackbar.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:vibration/vibration.dart';
import 'package:intl/intl.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:geolocator/geolocator.dart';
import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../../../data/repositories/device_repository.dart';
// ... rest of imports
import '../../../data/repositories/sensor_repository.dart';
import '../../../services/alarm_service.dart';
import '../../../services/location_service.dart';
import '../../../services/notification_service.dart';
import '../../../data/models/user_model.dart';
import '../../../data/models/device_model.dart';
import '../../settings/controllers/settings_controller.dart';
import '../../../data/repositories/weather_repository.dart';
import 'package:water_level_mobile/app/routes/app_pages.dart';

class HomeController extends GetxController
    with GetSingleTickerProviderStateMixin {
  final DeviceRepository _deviceRepo = Get.find<DeviceRepository>();
  final SensorRepository _sensorRepo = Get.find<SensorRepository>();
  final AlarmService _alarmService = Get.find<AlarmService>();
  final LocationService _locationService = Get.find<LocationService>();
  final WeatherRepository _weatherRepo = Get.find<WeatherRepository>();
  
  final _storage = GetStorage();
  
  // ── Loading & Network ───────────────────────────────────────────
  var isLoading = true.obs;
  var isRefreshing = false.obs;
  var hasInternet = true.obs;
  var pullDistance = 0.0.obs;
  var isManualRefreshing = false.obs;
  var isSearchingNode = false.obs;
  var scrollOffset = 0.0.obs;
  var defaultDeviceSlug = ''.obs;

  // ── Device list (dropdown) ───────────────────────────────────────
  var devices = <DeviceModel>[].obs;
  var selectedDeviceSlug = ''.obs;
  var selectedDeviceName = 'Pilih Node...'.obs;
  var selectedDeviceLocation = ''.obs;
  var selectedDeviceLat = 0.0.obs;
  var selectedDeviceLng = 0.0.obs;

  // ── User Data ────────────────────────────────────────────────────
  var userName = 'GUEST'.obs;
  var userEmail = ''.obs;
  var userPhotoUrl = ''.obs;
  var isGuest = true.obs;

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
  var sparklineTimestamps = <DateTime>[].obs;
  var _previousWaterLevel = 0.0;

  // ── Sensor Stats (24h) ──────────────────────────────────────────
  var avgDistance = 0.0.obs;
  var minDistance = 0.0.obs;
  var maxDistance = 0.0.obs;

  var avgWaterLevel = 0.0.obs;
  var minWaterLevel = 0.0.obs;
  var maxWaterLevel = 0.0.obs;

  // ── Status siaga ─────────────────────────────────────────────────
  var statusSiaga = 'MENYAMBUNG...'.obs;
  var statusColor = 0xFF3B82F6.obs; // Blue for connecting
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
  var weatherIcon = ''.obs;
  var weatherCode = 0.obs;
  var weatherWindspeed = 0.0.obs;
  var weatherHumidity = 0.obs;
  var weatherLocationName = '...'.obs;
  
  // ── AI Insights ──────────────────────────────────────────────────
  var aiRecommendation = 'Memantau kondisi...'.obs;
  var aiEta = '--'.obs;

  // ── Alarm ────────────────────────────────────────────────────────
  var isAlarmMuted = false.obs;
  // ── Animation ──────────────────────────────────────────────────
  var wavePhase = 0.0.obs;
  final cityImg = Rxn<ui.Image>();
  late AnimationController _waveController;
  Timer? _dataTimer;
  Timer? _weatherTimer;
  Timer? _statusDebounceTimer;
  Timer? _defaultNodeTimer;
  Timer? _snoozeTimer;
  String _pendingStatus = '';
  String _lastNotifiedDefaultStatus = '';
  var isSiaga1Snoozed = false.obs;
  bool _isSiaga1Acknowledged = false;
  bool _isMonitoringStarted = false;

  @override
  void onInit() {
    super.onInit();
    _loadCityImg();
    _loadSavedDevice();
    _loadUser(); 
    defaultDeviceSlug.value = _storage.read<String>('default_device_slug') ?? '';

    // Dengarkan perubahan storage secara langsung (khusus data user)
    _storage.listenKey('user', (value) {
      _loadUser();
    });

    // Auto-start if already logged in
    _checkLoginStatus();
    
    // Animation Ticker (Smooth 60fps, synced with screen)
    _waveController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 2),
    )..repeat();

    _waveController.addListener(() {
      wavePhase.value =
          _waveController.value * 2 * 3.14159; // Full phase rotation
    });

    // Monitor Internet Connection
    Connectivity()
        .onConnectivityChanged
        .listen((List<ConnectivityResult> results) {
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

  Future<void> _checkLoginStatus() async {
    const secureStorage = FlutterSecureStorage();
    final token = await secureStorage.read(key: 'token');
    if (token != null || _storage.read('is_guest') == true) {
      startMonitoring();
    }
  }

  Future<void> startMonitoring() async {
    if (_isMonitoringStarted) return;
    _isMonitoringStarted = true;

    debugPrint('DEBUG: Starting monitoring services...');

    // Initial fetches - await fetchDevices to ensure location logic can find closest
    await fetchDevices();
    _initLocationFromProfile();
    _fetchWeather();
    fetchSensorStats();

    _startMonitoringTimers();
  }

  void _startMonitoringTimers() {
    // Refresh sensor data every 5 sec
    _dataTimer?.cancel();
    _dataTimer = Timer.periodic(const Duration(seconds: 5), (_) {
      if (selectedDeviceSlug.value.isNotEmpty) fetchSensorData();
    });

    // Refresh weather every 10 min
    _weatherTimer?.cancel();
    _weatherTimer = Timer.periodic(const Duration(minutes: 10), (_) {
      _fetchWeather();
    });

    // Background monitoring for Default Node (if different from selected)
    _defaultNodeTimer?.cancel();
    _defaultNodeTimer = Timer.periodic(const Duration(seconds: 30), (_) {
      _monitorDefaultNode();
    });
  }

  void stopMonitoring() {
    _isMonitoringStarted = false;
    _dataTimer?.cancel();
    _weatherTimer?.cancel();
    _defaultNodeTimer?.cancel();
    _snoozeTimer?.cancel();
    _dataTimer = null;
    _weatherTimer = null;
    _defaultNodeTimer = null;
    _snoozeTimer = null;

    // Clear state
    devices.clear();
    selectedDeviceSlug.value = '';
    selectedDeviceName.value = 'Memuat...';
    sparklineData.clear();

    debugPrint('DEBUG: Monitoring services stopped.');
  }

  @override
  void onClose() {
    stopMonitoring();
    _waveController.dispose();
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
      debugPrint('Error fetching city image: $e');
    }
  }

  Future<void> fetchSensorStats() async {
    if (selectedDeviceSlug.value.isEmpty) return;
    try {
      final stats = await _sensorRepo.getStats(selectedDeviceSlug.value);
      if (stats != null) {
        avgWaterLevel.value = (stats['avg_water_level'] as num?)?.toDouble() ?? 0.0;
        minWaterLevel.value = (stats['min_water_level'] as num?)?.toDouble() ?? 0.0;
        maxWaterLevel.value = (stats['max_water_level'] as num?)?.toDouble() ?? 0.0;

        avgDistance.value = (stats['avg_distance'] as num?)?.toDouble() ?? 0.0;
        minDistance.value = (stats['min_distance'] as num?)?.toDouble() ?? 0.0;
        maxDistance.value = (stats['max_distance'] as num?)?.toDouble() ?? 0.0;
      }
    } catch (e) {
      debugPrint('Error fetching stats: $e');
    }
  }



  // ── Device management ────────────────────────────────────────────

  void _loadSavedDevice() {
    final savedSlug = _storage.read<String>('selected_device_slug');
    if (savedSlug != null && savedSlug.isNotEmpty) {
      selectedDeviceSlug.value = savedSlug;
    }
  }

  void _loadUser() {
    try {
      final userData = _storage.read('user');
      if (userData != null) {
        final user = UserModel.fromJson(userData);
        final String fullName = user.name;
        // Ambil nama depan saja, capitalize
        String firstName = fullName.split(' ')[0];
        if (firstName.length > 12) firstName = firstName.substring(0, 12);
        userName.value = firstName.toUpperCase();
        userEmail.value = user.email;
        userPhotoUrl.value = user.photoUrl ?? '';

        // Extract location from profile if available
        if (user.latitude != null && user.longitude != null) {
          userLat.value = user.latitude!;
          userLng.value = user.longitude!;
        }

        isGuest.value = false;
        debugPrint('DEBUG: Home User Loaded - ${userName.value}');
      } else {
        isGuest.value = true;
        userName.value = 'GUEST';
        userEmail.value = '';
        userPhotoUrl.value = '';
        debugPrint('DEBUG: Home User is GUEST');
      }
    } catch (e) {
      isGuest.value = true;
      userName.value = 'GUEST';
      userPhotoUrl.value = '';
      debugPrint('Error loading user: $e');
    }
  }

  Future<void> fetchDevices() async {
    if (devices.isEmpty) isLoading.value = true;
    try {
      final list = await _deviceRepo.getDevices();
      if (list.isNotEmpty) {
        devices.assignAll(list);

        // 1. Priority: User's explicitly set DEFAULT device (from Settings)
        final preferredDefaultSlug = _storage.read<String>('default_device_slug');

        if (preferredDefaultSlug != null && preferredDefaultSlug.isNotEmpty) {
            final preferred = list.firstWhere(
              (d) => d.slug == preferredDefaultSlug,
              orElse: () => list.first,
            );
            _selectDevice(preferred);
          debugPrint(
              'DEBUG: Using user preferred default device: $preferredDefaultSlug');
        }
        // 2. Secondary Priority: Closest node based on Profile Location
        else if (userLat.value != 0.0 && userLng.value != 0.0) {
          _sortAndSelectClosestDevice(userLat.value, userLng.value);
          debugPrint(
              'DEBUG: No default set. Using closest node based on profile.');
        }
        // 3. Fallback: First device in the list
        else {
          _selectDevice(list.first);
          debugPrint('DEBUG: Absolute fallback to first device in list.');
        }
      }
    } catch (e) {
      debugPrint('Error fetching devices: $e');
    } finally {
      isLoading.value = false;
    }
  }

  void _selectDevice(DeviceModel device) {
    final newSlug = device.slug;
    final isNewDevice = newSlug != selectedDeviceSlug.value;

    // 1. Reset & Show skeleton only if it's a DIFFERENT device or first time
    if (isNewDevice || sparklineData.isEmpty) {
      isLoading.value = true;
      sparklineData.clear();
      _previousWaterLevel = 0.0;
    }
    _lastNotifiedStatus = ''; // Reset alert memory
    _pendingStatus = '';
    _statusDebounceTimer?.cancel();
    _statusDebounceTimer = null;
    _stopAllAlarms(); // Stop any active alarms from previous device

    // Reset stats to 0 to avoid showing old device stats while loading
    avgWaterLevel.value = 0.0;
    minWaterLevel.value = 0.0;
    maxWaterLevel.value = 0.0;
    distance.value = 0.0;
    waterLevel.value = 0.0;
    statusSiaga.value = 'MENYAMBUNG...';
    statusColor.value = 0xFF3B82F6;
    _resetSensorData();

    // 2. Set new device info
    selectedDeviceSlug.value = device.slug;
    selectedDeviceName.value = device.name;
    selectedDeviceLocation.value = device.location ?? '';
    selectedDeviceLat.value = device.latitude ?? 0.0;
    selectedDeviceLng.value = device.longitude ?? 0.0;

    // 3. Persist and Fetch
    _storage.write('selected_device_slug', selectedDeviceSlug.value);

    // Fetch immediately
    fetchSensorData();
    fetchSensorStats();
    _fetchWeather();
    _startMonitoringTimers();
  }

  void setAsDefault(DeviceModel device) {
    final slug = device.slug;
    if (slug.isNotEmpty) {
      _storage.write('default_device_slug', slug);
      defaultDeviceSlug.value = slug;
      
      try {
        final settings = Get.find<SettingsController>();
        settings.defaultDeviceName.value = device.name;
      } catch (_) {}

      AppSnackbar.show(
        title: 'Node Utama Disetel',
        message: '${device.name} kini menjadi perangkat utama Anda.',
      );
    }
  }

  void toggleDefault(DeviceModel device) {
    final slug = device.slug;
    final currentDefault = _storage.read<String>('default_device_slug');

    if (currentDefault == slug) {
      // Unset/Unpin
      _storage.remove('default_device_slug');
      defaultDeviceSlug.value = '';
      
      try {
        final settings = Get.find<SettingsController>();
        settings.defaultDeviceName.value = 'Node Terdekat (Auto)';
      } catch (_) {}

      AppSnackbar.show(
        title: 'Node Utama Dihapus',
        message: 'Kembali menggunakan node terdekat secara otomatis.',
      );
    } else {
      // Set/Pin
      setAsDefault(device);
    }
  }

  String getDefaultDeviceSlug() {
    return _storage.read<String>('default_device_slug') ?? '';
  }

  void onDeviceSelected(DeviceModel device) {
    _selectDevice(device);
  }

  // ── Sensor data ──────────────────────────────────────────────────

  Future<void> fetchSensorData() async {
    if (selectedDeviceSlug.value.isEmpty) return;
    try {
      final response = await _sensorRepo.getLatestData(selectedDeviceSlug.value);
      if (response != null) {
        distance.value = response.distance;
        validCount.value = response.validCount;

        if (response.createdAt != null) {
          lastUpdated.value = DateFormat('HH:mm:ss').format(response.createdAt!.toLocal());
          
          // Heartbeat check
          isOnline.value = DateTime.now().toUtc().difference(response.createdAt!).inSeconds <= 30;
        } else {
          lastUpdated.value = '-- : --';
          isOnline.value = false;
        }

        // Update calibration config from response if available
        if (response.elevationMdpl != null) elevationMdpl.value = response.elevationMdpl!;
        if (response.sensorToBank != null) sensorToBank.value = response.sensorToBank!;
        if (response.riverDepth != null) riverDepth.value = response.riverDepth!;

        // Compute derived values ONLY if online
        if (isOnline.value) {
          waterLevel.value = elevationMdpl.value - (distance.value / 100.0);
          distanceToGround.value = sensorToBank.value - distance.value;

          if (_previousWaterLevel > 0) {
            final delta = waterLevel.value - _previousWaterLevel;
            flowVelocity.value = delta.abs();
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
          sparklineTimestamps.add(DateTime.now());
          if (sparklineData.length > 40) {
            sparklineData.removeAt(0);
            sparklineTimestamps.removeAt(0);
          }
        } else {
          _resetSensorData();
        }

          _updateStatusSiaga();
        _updateAiEta();
        
        // Check if we need to re-trigger Siaga 1 modal due to snooze expiry
        if (statusSiaga.value == 'SIAGA 1' && !isGuest.value && !isSiaga1Snoozed.value && !_isSiaga1Acknowledged) {
           if (Get.isDialogOpen != true) _showEvacuationDialog();
        }
      } else {
        isOnline.value = false;
        _resetSensorData();
        _updateStatusSiaga();
        _updateAiEta();
      }
    } catch (_) {
      isOnline.value = false;
      _resetSensorData();
      _updateStatusSiaga();
      _updateAiEta();
    } finally {
      isLoading.value = false;
      isRefreshing.value = false;
    }
  }

  void _stopAllAlarms() {
    _alarmService.stopAllAlarms();
  }

  void _resetSensorData() {
    flowVelocity.value = 0.0;
    flowTrend.value = 0;
    distanceToGround.value = -999.0;
    etaOverflow.value = '---';
  }

  void _updateAiEta() {
    final velocity = flowVelocity.value;
    final level = waterLevel.value;
    final status = statusSiaga.value;

    if (velocity > 0 && status != 'AMAN' && status != 'OFFLINE') {
      final remaining = 2.0 - level; // Assume 2.0m is overflow threshold
      if (remaining > 0) {
        // velocity is in m/sample (5s interval), so velocity/5 is m/s
        final minutes = (remaining / (velocity / 5)) / 60;
        if (minutes < 60) {
          aiEta.value = '${minutes.toInt()} Menit';
        } else if (minutes < 120) {
          aiEta.value = '1-2 Jam';
        } else {
          aiEta.value = '> 2 Jam';
        }
      } else {
        aiEta.value = 'MELUAP';
      }
    } else {
      aiEta.value = '--';
    }
    
    etaOverflow.value = aiEta.value;
    _updateAiInsights();
  }

  void _updateAiInsights() {
    final status = statusSiaga.value;
    
    if (status == 'AMAN') {
      aiRecommendation.value = 'Kondisi air terpantau normal. Tetap waspada jika terjadi hujan deras di hulu.';
    } else if (status == 'SIAGA 3') {
      aiRecommendation.value = 'Kenaikan terpantau. Pastikan saluran drainase di sekitar Anda tidak tersumbat.';
    } else if (status == 'SIAGA 2') {
      aiRecommendation.value = 'Kondisi mulai kritis. Amankan dokumen berharga dan pantau pergerakan air secara intensif.';
    } else if (status == 'SIAGA 1') {
      aiRecommendation.value = 'BAHAYA! Segera lakukan evakuasi mandiri ke tempat yang lebih tinggi. Ikuti arahan petugas.';
    } else {
      aiRecommendation.value = 'Memantau kondisi...';
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
      _isSiaga1Acknowledged = false; // Reset on offline
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
      _isSiaga1Acknowledged = false; // Reset when status becomes AMAN
    }

    // Always update color for immediate UI feedback on the gauge
    statusColor.value = newColor;

    // 1. INSTANT UPDATE: If currently OFFLINE, CONNECTING or switching TO OFFLINE, update immediately
    if (statusSiaga.value == 'OFFLINE' ||
        statusSiaga.value == 'MENYAMBUNG...' ||
        !isOnline.value) {
      _statusDebounceTimer?.cancel();
      _statusDebounceTimer = null;
      statusSiaga.value = newStatus;
      _pendingStatus = newStatus;
      if (isOnline.value) _triggerAlert();
      return;
    }

    // 2. If status matches current confirmed status, cancel pending changes
    if (newStatus == statusSiaga.value) {
      _statusDebounceTimer?.cancel();
      _statusDebounceTimer = null;
      _pendingStatus = newStatus;
      return;
    }

    // 3. DEBOUNCE: For transitions between active statuses (e.g. AMAN -> SIAGA 3),
    // wait 2 seconds to ensure stability before triggering alarms.
    if (newStatus != _pendingStatus) {
      _pendingStatus = newStatus;
      _statusDebounceTimer?.cancel();
      _statusDebounceTimer = Timer(const Duration(seconds: 2), () {
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
    _alarmService.stopAllAlarms(); // Cancel any existing snooze or alarms on status change
    
    // Reset Acknowledge if entering Siaga 1 from another status
    if (statusSiaga.value == 'SIAGA 1') {
      _isSiaga1Acknowledged = false;
      isSiaga1Snoozed.value = false;
      _snoozeTimer?.cancel();
    }

    // GUEST TREATMENT: Only visual color update (which is done in _updateStatusSiaga)
    // No notifications, no sound, no vibration for guests.
    if (isGuest.value) {
      debugPrint('DEBUG: Alert suppressed for Guest User');
      return;
    }

    // Notification
    switch (statusSiaga.value) {
      case 'SIAGA 1':
        await NotificationService.notifySiaga1();
        await _alarmService.playSiaga1Alarm();
        _showEvacuationDialog();
        break;
      case 'SIAGA 2':
        await NotificationService.notifySiaga2();
        _alarmService.startSiaga2Periodic(() => _alarmService.playWarningSound());
        break;
      case 'SIAGA 3':
        await NotificationService.notifySiaga3();
        _alarmService.startSiaga3Periodic(() => _alarmService.playWarningSound());
        break;
      default:
        _alarmService.stopAllAlarms();
    }

    _triggerVibration(statusSiaga.value);
  }

  void _triggerVibration(String status) async {
    if (kIsWeb) return;
    if (!await Vibration.hasVibrator()) return;

    if (status == 'SIAGA 1') {
      Vibration.vibrate(pattern: [500, 200, 500, 200, 1000], repeat: 0);
    } else if (status == 'SIAGA 2') {
      Vibration.vibrate(pattern: [300, 200, 300]);
    } else if (status == 'SIAGA 3') {
      Vibration.vibrate(duration: 400);
    }
  }

  Future<void> _monitorDefaultNode() async {
    if (isGuest.value) return;
    final defSlug = defaultDeviceSlug.value;
    if (defSlug.isEmpty || defSlug == selectedDeviceSlug.value) return;

    try {
      final response = await _sensorRepo.getLatestData(defSlug);
      if (response != null) {
        // Heartbeat check (30s)
        final isNodeOnline = DateTime.now().toUtc().difference(response.createdAt!).inSeconds <= 30;
        if (!isNodeOnline) return;

        final dist = response.distance;
        final sensorBank = response.sensorToBank ?? 100.0;
        final distToGround = sensorBank - dist;

        String defStatus = 'AMAN';
        if (distToGround >= 0) {
          defStatus = 'SIAGA 1';
        } else if (distToGround >= -20) {
          defStatus = 'SIAGA 2';
        } else if (distToGround >= -50) {
          defStatus = 'SIAGA 3';
        }

        if (defStatus != 'AMAN' && defStatus != _lastNotifiedDefaultStatus) {
          _lastNotifiedDefaultStatus = defStatus;
          
          // Get node name
          final node = devices.firstWhereOrNull((d) => d.slug == defSlug);
          final nodeName = node?.name ?? 'Node Utama';

          // Background notification only (no sound/modal to avoid conflict with current view)
          if (defStatus == 'SIAGA 1') {
            await NotificationService.notifySiaga1(customTitle: 'BAHAYA: $nodeName');
          } else if (defStatus == 'SIAGA 2') {
            await NotificationService.notifySiaga2(customTitle: 'PERINGATAN: $nodeName');
          } else if (defStatus == 'SIAGA 3') {
            await NotificationService.notifySiaga3(customTitle: 'WASPADA: $nodeName');
          }
        } else if (defStatus == 'AMAN') {
          _lastNotifiedDefaultStatus = 'AMAN';
        }
      }
    } catch (_) {}
  }

  void _showEvacuationDialog() {
    // Prevent modal stacking or guest display
    if (Get.isDialogOpen == true || isGuest.value) return;

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
        actionsPadding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
        actions: [
          Column(
            children: [
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: () {
                    _isSiaga1Acknowledged = true;
                    isSiaga1Snoozed.value = false;
                    _snoozeTimer?.cancel();
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
                  child: Text(
                    'SAYA SUDAH EVAKUASI',
                    style: GoogleFonts.inter(fontWeight: FontWeight.w900),
                  ),
                ),
              ),
              const SizedBox(height: 8),
              SizedBox(
                width: double.infinity,
                child: TextButton(
                  onPressed: () {
                    _snoozeSiaga1();
                    Get.back();
                  },
                  style: TextButton.styleFrom(
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 12),
                  ),
                  child: Text(
                    'Ingatkan 5 Menit Lagi',
                    style: GoogleFonts.inter(
                        fontWeight: FontWeight.w600, color: Colors.white70),
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  void _snoozeSiaga1() {
    isSiaga1Snoozed.value = true;
    _stopAllAlarms();
    _snoozeTimer?.cancel();
    _snoozeTimer = Timer(const Duration(minutes: 5), () {
      isSiaga1Snoozed.value = false;
      // Modal will be re-triggered by fetchSensorData() check
    });
    AppSnackbar.show(
      title: 'Alarm Ditunda',
      message: 'Peringatan akan muncul kembali dalam 5 menit.',
    );
  }

  void showGuestRestrictionModal() {
    Get.dialog(
      Dialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        child: Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(24),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: const Color(0xFFF1F5F9),
                  shape: BoxShape.circle,
                ),
                child: Icon(Icons.lock_person_rounded,
                    color: const Color(0xFF0F172A), size: 32),
              ),
              const SizedBox(height: 20),
              Text(
                'Fitur Terbatas',
                style: GoogleFonts.inter(
                  fontSize: 20,
                  fontWeight: FontWeight.w900,
                  color: const Color(0xFF0F172A),
                ),
              ),
              const SizedBox(height: 12),
              Text(
                'Daftar atau Login untuk menikmati fitur lengkap seperti Notifikasi Otomatis, Alarm Bahaya, dan Ekspor Data Analisis.',
                textAlign: TextAlign.center,
                style: GoogleFonts.inter(
                  fontSize: 14,
                  color: const Color(0xFF64748B),
                  height: 1.5,
                ),
              ),
              const SizedBox(height: 32),
              Row(
                children: [
                  Expanded(
                    child: TextButton(
                      onPressed: () => Get.back(),
                      child: Text(
                        'Nanti Saja',
                        style: GoogleFonts.inter(
                          fontWeight: FontWeight.w700,
                          color: const Color(0xFF64748B),
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: ElevatedButton(
                      onPressed: () {
                        Get.back();
                        Get.offAllNamed(Routes.LOGIN);
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF0F172A),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12)),
                      ),
                      child: Text(
                        'Masuk Sekarang',
                        style: GoogleFonts.inter(fontWeight: FontWeight.w800),
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
  // ── ETA Calculation ──────────────────────────────────────────────



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
    final data = await _weatherRepo.fetchWeather(
      latitude: targetLat,
      longitude: targetLng,
    );
    if (data != null) {
      weatherTemp.value = (data['temperature'] as num).toDouble();
      weatherDesc.value = (data['description'] as String).toUpperCase();
      weatherIcon.value = data['icon'] as String;
      weatherCode.value = data['weathercode'] as int;
      weatherWindspeed.value = (data['windspeed'] as num).toDouble();
      weatherHumidity.value = (data['humidity'] as num).toInt();

      if (data['locationName'] != null) {
        final locName = data['locationName'].toString();
        weatherLocationName.value = locName.toUpperCase();
      }
    }
    weatherLoading.value = false;
  }

  // ── User Location Logic ──────────────────────────────────────────

  Future<void> _initLocationFromProfile() async {
    final userData = _storage.read('user');
    if (userData != null) {
      final user = UserModel.fromJson(userData);
      if (user.latitude != null && user.longitude != null) {
        userLat.value = user.latitude!;
        userLng.value = user.longitude!;
        
        final address = await _locationService.getAddressFromCoordinates(user.latitude!, user.longitude!);
        if (address != null) userAddress.value = address;

        if (selectedDeviceSlug.value.isEmpty) {
          _sortAndSelectClosestDevice(user.latitude!, user.longitude!);
        }
        return;
      }
    }

    final lastLat = _storage.read<double>('last_user_lat');
    final lastLng = _storage.read<double>('last_user_lng');
    if (lastLat != null && lastLng != null) {
      userLat.value = lastLat;
      userLng.value = lastLng;
      
      final address = await _locationService.getAddressFromCoordinates(lastLat, lastLng);
      if (address != null) userAddress.value = address;

      if (selectedDeviceSlug.value.isEmpty) {
        _sortAndSelectClosestDevice(lastLat, lastLng);
      }
    }
  }

  void _sortAndSelectClosestDevice(double userLat, double userLng, {bool forceSelect = false, bool isDefaultMode = false}) {
    if (devices.isEmpty) return;

    final sortedList = List<DeviceModel>.from(devices);
    sortedList.sort((a, b) {
      final latA = a.latitude ?? 0.0;
      final lngA = a.longitude ?? 0.0;
      final latB = b.latitude ?? 0.0;
      final lngB = b.longitude ?? 0.0;

      final distA = Geolocator.distanceBetween(userLat, userLng, latA, lngA);
      final distB = Geolocator.distanceBetween(userLat, userLng, latB, lngB);
      return distA.compareTo(distB);
    });

    devices.value = sortedList;
    final closest = sortedList.first;

    // Simpan sebagai default jika isDefaultMode aktif atau jika user belum punya default
    if (isDefaultMode || _storage.read<String>('default_device_slug') == null) {
      final slug = closest.slug;
      if (slug.isNotEmpty) {
        _storage.write('default_device_slug', slug);
        defaultDeviceSlug.value = slug;
        
        // Update SettingsController name display if it exists
        try {
          final settings = Get.find<SettingsController>();
          settings.defaultDeviceName.value = closest.name;
        } catch (_) {}
        
        debugPrint('DEBUG: Auto-saved closest node as default: $slug');
      }
    }

    // Jika belum ada pilihan sesi, gunakan yang terdekat, ATAU jika dipaksa (via tombol manual)
    if (_storage.read<String>('selected_device_slug') == null || forceSelect) {
      _selectDevice(closest);
    }
  }

  Future<void> _getAddressFromCoords(double lat, double lng) async {
    final address = await _locationService.getAddressFromCoordinates(lat, lng);
    if (address != null) userAddress.value = address;
  }

  // ── Smart Auto-Selection Logic ──────────────────────────────────

  Future<void> autoSelectByProfile({bool isDefaultMode = false}) async {
    isSearchingNode.value = true;
    
    // Simulate searching delay for visual feedback
    await Future.delayed(const Duration(milliseconds: 1500));

    final userData = _storage.read('user');
    if (userData != null) {
      final user = UserModel.fromJson(userData);
      if (user.latitude != null && user.longitude != null) {
        final double lat = user.latitude!;
        final double lng = user.longitude!;

        if (lat != 0.0 && lng != 0.0) {
          userLat.value = lat;
          userLng.value = lng;
          await _getAddressFromCoords(lat, lng);
          _sortAndSelectClosestDevice(lat, lng, forceSelect: true, isDefaultMode: isDefaultMode);
          
          isSearchingNode.value = false;
          Get.back(); // Close bottom sheet after success

          AppSnackbar.show(
            title: 'Sinkronisasi Profil',
            message: 'Berhasil menemukan node terdekat dari domisili Anda.',
          );
        } else {
          // Fallback ke acak jika profil tidak punya koordinat
          await selectRandomDevice(isDefaultMode: isDefaultMode);
        }
      } else {
        // Fallback ke acak jika profil tidak punya koordinat
        await selectRandomDevice(isDefaultMode: isDefaultMode);
      }
    } else {
      // Fallback ke acak jika user adalah Guest
      await selectRandomDevice(isDefaultMode: isDefaultMode);
    }
  }

  Future<void> autoSelectByGPS({bool isDefaultMode = false}) async {
    isSearchingNode.value = true;
    bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) {
      // Fallback ke acak jika GPS mati
      await selectRandomDevice(isDefaultMode: isDefaultMode);
      return;
    }

    LocationPermission permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.denied) {
        // Fallback ke acak jika izin ditolak
        await selectRandomDevice(isDefaultMode: isDefaultMode);
        return;
      }
    }

    if (permission == LocationPermission.deniedForever) {
      AppSnackbar.show(
        title: 'Izin Ditolak',
        message: 'Izinkan akses lokasi melalui pengaturan aplikasi.',
        isError: true,
      );
      isSearchingNode.value = false;
      return;
    }

    try {
      Position position = await Geolocator.getCurrentPosition(
        locationSettings: const LocationSettings(
          accuracy: LocationAccuracy.high,
          timeLimit: Duration(seconds: 15),
        ),
      );
      
      userLat.value = position.latitude;
      userLng.value = position.longitude;
      
      await _getAddressFromCoords(position.latitude, position.longitude);
      _sortAndSelectClosestDevice(position.latitude, position.longitude, forceSelect: true, isDefaultMode: isDefaultMode);

      isSearchingNode.value = false;
      Get.back(); // Close bottom sheet after success

      AppSnackbar.show(
        title: 'Sinkronisasi GPS',
        message: 'Berhasil menemukan node terdekat dari lokasi Anda saat ini.',
      );
    } catch (e) {
      // Fallback ke acak jika terjadi error (misal timeout GPS)
      await selectRandomDevice(isDefaultMode: isDefaultMode);
    } finally {
      isSearchingNode.value = false;
    }
  }

  Future<void> selectRandomDevice({bool isDefaultMode = false}) async {
    if (devices.isEmpty) return;

    isSearchingNode.value = true;
    
    // Memberikan feedback visual seolah sedang memilih
    await Future.delayed(const Duration(milliseconds: 800));

    final random = DateTime.now().millisecond % devices.length;
    final device = devices[random];

    onDeviceSelected(device);
    
    if (isDefaultMode) {
      final slug = device.slug;
      _storage.write('default_device_slug', slug);
      defaultDeviceSlug.value = slug;
      
      try {
        final settings = Get.find<SettingsController>();
        settings.defaultDeviceName.value = device.name;
      } catch (_) {}
    }

    isSearchingNode.value = false;
    Get.back();

    AppSnackbar.show(
      title: 'Mode Eksplorasi',
      message: 'Berhasil memilih "${device.name}" secara acak untuk Anda.',
    );
  }
}
