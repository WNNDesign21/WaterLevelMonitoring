import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'app/routes/app_pages.dart';

void main() async {
  await GetStorage.init();
  runApp(
    GetMaterialApp(
      title: "Monitoring Air",
      initialRoute: AppPages.INITIAL,
      getPages: AppPages.routes,
      theme: ThemeData.light().copyWith(
        primaryColor: const Color(0xFF3B82F6),
        scaffoldBackgroundColor: const Color(0xFFF8FAFC),
      ),
      debugShowCheckedModeBanner: false,
    ),
  );
}
