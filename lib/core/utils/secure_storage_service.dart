import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class SecureStorageService {
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  Future<void> saveToken(String token) async {
    await _storage.write(key: 'auth_token', value: token);
  }

  Future<String?> getToken() async {
    return await _storage.read(key: 'auth_token');
  }

  Future<void> deleteToken() async {
    await _storage.delete(key: 'auth_token');
  }

  Future<void> saveUser(String userData) async {
    await _storage.write(key: 'user_data', value: userData);
  }

  Future<String?> getUser() async {
    return await _storage.read(key: 'user_data');
  }

  Future<void> deleteUser() async {
    await _storage.delete(key: 'user_data');
  }

  Future<void> clearAll() async {
    await _storage.deleteAll();
  }
} 