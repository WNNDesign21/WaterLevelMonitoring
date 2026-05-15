import 'dart:async';
import 'package:app_links/app_links.dart';
import 'package:get/get.dart';
import '../../routes/app_pages.dart';

class DeepLinkService extends GetxService {
  late AppLinks _appLinks;
  StreamSubscription<Uri>? _linkSubscription;

  Future<DeepLinkService> init() async {
    _appLinks = AppLinks();
    
    // Check for initial link when app is closed
    final initialLink = await _appLinks.getInitialLink();
    if (initialLink != null) {
      _handleDeepLink(initialLink);
    }

    // Listen to incoming links while app is open/in background
    _linkSubscription = _appLinks.uriLinkStream.listen((uri) {
      _handleDeepLink(uri);
    });

    return this;
  }

  void _handleDeepLink(Uri uri) {
    print('DEBUG: Received Deep Link: $uri');
    
    if (uri.scheme == 'watersense' && uri.host == 'reset-password') {
      final token = uri.queryParameters['token'];
      final email = uri.queryParameters['email'];

      if (token != null && email != null) {
        // Navigate to Reset Password page with arguments
        Get.toNamed(Routes.RESET_PASSWORD, arguments: {
          'token': token,
          'email': email,
        });
      }
    }
  }

  @override
  void onClose() {
    _linkSubscription?.cancel();
    super.onClose();
  }
}
