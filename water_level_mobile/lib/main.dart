import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'app/core/theme/app_theme.dart';
import 'app/core/theme/theme_controller.dart';
import 'app/routes/app_pages.dart';
import 'app/modules/home/controllers/home_controller.dart';
import 'app/data/providers/api_provider.dart';
import 'app/data/providers/weather_provider.dart';
import 'app/services/notification_service.dart';
import 'app/data/services/deep_link_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Initialize Deep Link Service early
  await Get.putAsync(() => DeepLinkService().init());
  
  // Load Environment Variables
  await dotenv.load(fileName: ".env");
  
  // Initialize Storage
  await GetStorage.init();
  
  // Initialize Notification Service
  await NotificationService.init();
  
  // Initialize Date Formatting for id_ID
  await initializeDateFormatting('id_ID', null);

  // Inject Providers
  Get.put(ApiProvider(), permanent: true);
  Get.put(WeatherProvider(), permanent: true);

  // Inject Controllers
  Get.put(ThemeController(), permanent: true);
  Get.put(HomeController(), permanent: true);

  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    final themeController = Get.find<ThemeController>();

    // Configure System UI
    SystemChrome.setSystemUIOverlayStyle(
      SystemUiOverlayStyle(
        statusBarColor: Colors.transparent,
        statusBarIconBrightness: themeController.isDarkMode ? Brightness.light : Brightness.dark,
        systemNavigationBarColor: Colors.transparent,
        systemNavigationBarIconBrightness: themeController.isDarkMode ? Brightness.light : Brightness.dark,
      ),
    );

    return GetMaterialApp(
      title: 'WaterSense',
      initialRoute: AppPages.INITIAL,
      getPages: AppPages.routes,
      theme: AppTheme.light,
      darkTheme: AppTheme.dark,
      themeMode: themeController.isDarkMode ? ThemeMode.dark : ThemeMode.light,
      debugShowCheckedModeBanner: false,
      defaultTransition: Transition.cupertino,
    );
  }
}
