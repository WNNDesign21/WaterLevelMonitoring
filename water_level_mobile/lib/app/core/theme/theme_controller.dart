import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';

class ThemeController extends GetxController {
  final _box = GetStorage();
  final _key = 'isDarkMode';

  // Initialize directly with a literal to ensure .obs extension is recognized correctly.
  // This avoids NoSuchMethodError: 'obs' which can happen on Web with dynamic types.
  final RxBool _isDarkMode = false.obs;
  
  bool get isDarkMode => _isDarkMode.value;

  @override
  void onInit() {
    super.onInit();
    // Load saved value after initialization
    final saved = _box.read(_key);
    if (saved != null) {
      _isDarkMode.value = saved as bool;
    }
  }

  void toggleTheme() {
    _isDarkMode.value = !_isDarkMode.value;
    Get.changeThemeMode(_isDarkMode.value ? ThemeMode.dark : ThemeMode.light);
    _box.write(_key, _isDarkMode.value);
  }
}
