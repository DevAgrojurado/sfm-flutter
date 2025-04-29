import 'package:dio/dio.dart';
import 'package:sfm_app/core/api/api_client.dart';
import 'package:sfm_app/core/error/exceptions.dart';
import 'package:sfm_app/features/auth/data/models/user_model.dart';

abstract class AuthRemoteDataSource {
  /// Inicia sesión con email y contraseña
  /// Lanza [ServerException] en caso de error
  Future<Map<String, dynamic>> login(String email, String password);

  /// Cierra sesión en la API
  /// Lanza [ServerException] en caso de error
  Future<bool> logout(String token);
}

class AuthRemoteDataSourceImpl implements AuthRemoteDataSource {
  final ApiClient apiClient;

  AuthRemoteDataSourceImpl({required this.apiClient});

  @override
  Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final response = await apiClient.post(
        '/api/login',
        data: {
          'email': email,
          'password': password,
        },
      );

      if (response.statusCode == 200) {
        print('Login exitoso: ${response.data}');
        final userData = response.data['usuario'];
        final token = response.data['token'];
        final permisosData = response.data['permisos'];

        return {
          'user': UserModel.fromJson(userData, permisosData: permisosData),
          'token': token,
        };
      } else {
        throw ServerException(message: 'Error en la autenticación: Código ${response.statusCode}');
      }
    } on DioException catch (e) {
      print('Error DioException: ${e.message}, tipo: ${e.type}, código: ${e.response?.statusCode}');
      if (e.response?.statusCode == 401) {
        throw AuthenticationException(message: 'Credenciales inválidas');
      } else if (e.response?.statusCode == 403) {
        throw AuthenticationException(message: 'Usuario no vigente');
      } else if (e.type == DioExceptionType.connectionTimeout) {
        throw ConnectionException(message: 'Tiempo de espera agotado');
      } else {
        throw ServerException(message: e.message ?? 'Error desconocido');
      }
    } catch (e) {
      print('Error general en login: $e');
      throw ServerException(message: e.toString());
    }
  }

  @override
  Future<bool> logout(String token) async {
    try {
      apiClient.setAuthToken(token);
      final response = await apiClient.post('/api/logout');
      apiClient.clearAuthToken();
      
      return response.statusCode == 200;
    } on DioException catch (e) {
      throw ServerException(message: e.message ?? 'Error al cerrar sesión');
    } catch (e) {
      throw ServerException(message: e.toString());
    }
  }
} 