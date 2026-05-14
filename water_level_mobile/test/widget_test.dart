import 'package:flutter_test/flutter_test.dart';
import 'package:water_level_mobile/main.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:water_level_mobile/app/core/theme/theme_controller.dart';

void main() {
  testWidgets('App smoke test', (WidgetTester tester) async {
    // Initialize required services for testing
    await GetStorage.init();
    Get.put(ThemeController());

    // Build our app and trigger a frame.
    await tester.pumpWidget(const MyApp());

    // Basic check to see if the app is rendered
    expect(find.byType(MyApp), findsOneWidget);
  });
}
