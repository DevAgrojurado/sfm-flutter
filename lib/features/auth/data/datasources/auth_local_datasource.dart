import 'dart:convert';

import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:sfm_app/core/error/exceptions.dart';
import 'package:sfm_app/features/auth/data/models/user_model.dart';

abstract class AuthLocalDataSource {
  /// Obtiene el usuario guardado localmente
  /// Lanza [CacheException] si no hay datos
  Future<UserModel?> getLastUser();

  /// Guarda el usuario localmente
  Future<void> cacheUser(UserModel user);

  /// Guarda el token de autenticación
  Future<void> cacheToken(String token);

  /// Obtiene el token de autenticación
  /// Lanza [CacheException] si no hay token
  Future<String?> getToken();

  /// Limpia los datos de autenticación
  Future<void> clearAuthData();
}

class AuthLocalDataSourceImpl implements AuthLocalDataSource {
  final FlutterSecureStorage secureStorage;

  AuthLocalDataSourceImpl({required this.secureStorage});

  @override
  Future<void> cacheToken(String token) async {
    try {
      await secureStorage.write(key: 'AUTH_TOKEN', value: token);
    } catch (e) {
      throw CacheException(message: 'Error al guardar el token: $e');
    }
  }

  @override
  Future<void> cacheUser(UserModel user) async {
    try {
      final jsonUser = jsonEncode(user.toJson());
      await secureStorage.write(key: 'CACHED_USER', value: jsonUser);
    } catch (e) {
      throw CacheException(message: 'Error al guardar el usuario: $e');
    }
  }

  @override
  Future<void> clearAuthData() async {
    try {
      await secureStorage.delete(key: 'AUTH_TOKEN');
      await secureStorage.delete(key: 'CACHED_USER');
    } catch (e) {
      throw CacheException(message: 'Error al limpiar los datos de autenticación: $e');
    }
  }

  @override
  Future<String?> getToken() async {
    try {
      final token = await secureStorage.read(key: 'AUTH_TOKEN');
      return token;
    } catch (e) {
      throw CacheException(message: 'Error al obtener el token: $e');
    }
  }

  @override
  Future<UserModel?> getLastUser() async {
    try {
      final jsonUser = await secureStorage.read(key: 'CACHED_USER');
      if (jsonUser == null) {
        return null;
      }
      return UserModel.fromJson(jsonDecode(jsonUser));
    } catch (e) {
      throw CacheException(message: 'Error al obtener el usuario: $e');
    }
  }
} 