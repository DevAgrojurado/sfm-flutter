import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:sfm_app/core/constants/api_constants.dart';
import 'package:sfm_app/features/auth/data/datasources/auth_local_datasource.dart';
import 'package:sfm_app/features/evaluaciones/domain/entities/evaluacion_general.dart';
import 'package:sfm_app/features/evaluaciones/domain/entities/evaluacion_polinizacion.dart';
import 'package:sfm_app/features/evaluaciones/domain/repositories/evaluaciones_repository.dart';

class EvaluacionesRepositoryImpl implements EvaluacionesRepository {
  final http.Client client;
  final AuthLocalDataSource authLocalDataSource;

  EvaluacionesRepositoryImpl({
    required this.client,
    required this.authLocalDataSource,
  });

  @override
  Future<List<EvaluacionGeneral>> getEvaluacionesGenerales() async {
    try {
      final token = await authLocalDataSource.getToken();
      if (token == null) throw Exception('Token no encontrado');
      
      final response = await client.get(
        Uri.parse('${ApiConstants.baseUrl}/evaluaciones-generales'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        final List<dynamic> decodedData = json.decode(response.body);
        return decodedData.map((json) => EvaluacionGeneral.fromJson(json)).toList();
      } else {
        throw Exception('Error al obtener evaluaciones generales: ${response.body}');
      }
    } catch (e) {
      throw Exception('Error al obtener evaluaciones generales: $e');
    }
  }

  @override
  Future<List<EvaluacionGeneral>> getEvaluacionesPorFinca(int fincaId) async {
    try {
      final token = await authLocalDataSource.getToken();
      if (token == null) throw Exception('Token no encontrado');
      
      final response = await client.get(
        Uri.parse('${ApiConstants.baseUrl}/fincas/$fincaId/evaluaciones-generales'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        final Map<String, dynamic> responseData = json.decode(response.body);
        final List<dynamic> evaluacionesData = responseData['evaluaciones'];
        return evaluacionesData.map((json) => EvaluacionGeneral.fromJson(json)).toList();
      } else {
        throw Exception('Error al obtener evaluaciones por finca: ${response.body}');
      }
    } catch (e) {
      throw Exception('Error al obtener evaluaciones por finca: $e');
    }
  }

  @override
  Future<List<EvaluacionPolinizacion>> getEvaluacionesPolinizacion() async {
    try {
      final token = await authLocalDataSource.getToken();
      if (token == null) throw Exception('Token no encontrado');
      
      final response = await client.get(
        Uri.parse('${ApiConstants.baseUrl}/evaluaciones-polinizacion'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        final List<dynamic> decodedData = json.decode(response.body);
        return decodedData.map((json) => EvaluacionPolinizacion.fromJson(json)).toList();
      } else {
        throw Exception('Error al obtener evaluaciones de polinización: ${response.body}');
      }
    } catch (e) {
      throw Exception('Error al obtener evaluaciones de polinización: $e');
    }
  }

  @override
  Future<List<EvaluacionPolinizacion>> getEvaluacionesPolinizacionPorEvaluacionGeneral(int evaluacionGeneralId) async {
    try {
      final token = await authLocalDataSource.getToken();
      if (token == null) throw Exception('Token no encontrado');
      
      final response = await client.get(
        Uri.parse('${ApiConstants.baseUrl}/evaluaciones-generales/$evaluacionGeneralId/evaluaciones-polinizacion'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        final List<dynamic> decodedData = json.decode(response.body);
        return decodedData.map((json) => EvaluacionPolinizacion.fromJson(json)).toList();
      } else {
        throw Exception('Error al obtener evaluaciones de polinización: ${response.body}');
      }
    } catch (e) {
      throw Exception('Error al obtener evaluaciones de polinización: $e');
    }
  }
} 