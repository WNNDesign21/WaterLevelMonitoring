import 'package:dio/dio.dart';
import 'package:get_storage/get_storage.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../models/user_model.dart';
import '../providers/base_provider.dart';

class AuthRepository extends BaseProvider {
  final _storage = GetStorage();
  final _secureStorage = const FlutterSecureStorage();

  Future<void> _saveAuth(String token, Map<String, dynamic> userData) async {
    await _secureStorage.write(key: 'token', value: token);
    await _storage.write('user', userData);
    await _storage.write('is_guest', false);
  }

  Future<UserModel?> login(String email, String password) async {
    try {
      final response = await dio.post('login', data: {
        'email': email,
        'password': password,
      });
      
      if (response.statusCode == 200) {
        final data = response.data['data'] ?? response.data;
        final token = data['token'];
        final userData = data['user'];
        
        await _saveAuth(token, userData);
        
        return UserModel.fromJson(userData);
      }
    } catch (e) {
      print('Login Error: $e');
    }
    return null;
  }

  Future<void> logout() async {
    await _secureStorage.delete(key: 'token');
    await _storage.remove('user');
    await _storage.write('is_guest', true);
  }

  Future<UserModel?> updateProfile(Map<String, dynamic> data) async {
    try {
      final response = await dio.post('update-profile', data: data);
      if (response.statusCode == 200) {
        final userData = response.data['data']['user'] ?? response.data['user'];
        await _storage.write('user', userData);
        return UserModel.fromJson(userData);
      }
    } catch (_) {}
    return null;
  }

  Future<Map<String, dynamic>?> register(Map<String, dynamic> data) async {
    try {
      final response = await dio.post('register', data: data);
      if (response.statusCode == 200 || response.statusCode == 201) {
        final body = response.data['data'] ?? response.data;
        await _saveAuth(body['token'], body['user']);
        return {
          'statusCode': response.statusCode,
          'data': response.data,
        };
      }
    } catch (e) {
      if (e is DioException && e.response != null) {
        return {
          'statusCode': e.response!.statusCode,
          'data': e.response!.data,
        };
      }
    }
    return null;
  }

  Future<Map<String, dynamic>?> googleLogin(Map<String, dynamic> data) async {
    try {
      final response = await dio.post('google-login', data: data);
      if (response.statusCode == 200) {
        final body = response.data['data'] ?? response.data;
        await _saveAuth(body['token'], body['user']);
        return body;
      }
    } catch (e) {
      print('Google Login API Error: $e');
    }
    return null;
  }

  Future<Map<String, dynamic>> forgotPassword(String email, String method) async {
    try {
      final response = await dio.post('forgot-password', data: {
        'email': email,
        'method': method,
      });
      return {
        'statusCode': response.statusCode,
        'data': response.data,
      };
    } catch (e) {
      return {'statusCode': 500, 'data': {'message': 'Terjadi kesalahan koneksi.'}};
    }
  }

  Future<Map<String, dynamic>> resetPassword(Map<String, dynamic> data) async {
    try {
      final response = await dio.post('reset-password', data: data);
      return {
        'statusCode': response.statusCode,
        'data': response.data,
      };
    } catch (e) {
      return {'statusCode': 500, 'data': {'message': 'Terjadi kesalahan koneksi.'}};
    }
  }

  Future<Map<String, dynamic>> completeProfile(Map<String, dynamic> data) async {
    try {
      final response = await dio.post('complete-profile', data: data);
      return {
        'statusCode': response.statusCode,
        'data': response.data,
      };
    } catch (e) {
      return {'statusCode': 500, 'data': {'message': 'Terjadi kesalahan koneksi.'}};
    }
  }
}
