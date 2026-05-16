import 'package:dio/dio.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:get/get.dart' as getx;
import 'package:get_storage/get_storage.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../../routes/app_pages.dart';

class BaseProvider {
  late Dio dio;
  final _secureStorage = const FlutterSecureStorage();

  BaseProvider() {
    var baseUrl = dotenv.env['API_BASE_URL'] ?? 'http://103.172.205.35/api';
    if (!baseUrl.endsWith('/')) {
      baseUrl = '$baseUrl/';
    }
    
    dio = Dio(BaseOptions(
      baseUrl: baseUrl,
      connectTimeout: const Duration(seconds: 15),
      receiveTimeout: const Duration(seconds: 15),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    ));

    // Interceptors for Logging and Auth
    dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await _secureStorage.read(key: 'token');
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        return handler.next(options);
      },
      onResponse: (response, handler) {
        return handler.next(response);
      },
      onError: (DioException e, handler) async {
        // Handle 401 Unauthorized (Token Expired)
        if (e.response?.statusCode == 401) {
          await _secureStorage.delete(key: 'token');
          final storage = GetStorage();
          await storage.remove('user');
          getx.Get.offAllNamed(Routes.LOGIN);
        }
        return handler.next(e);
      },
    ));
  }
}
