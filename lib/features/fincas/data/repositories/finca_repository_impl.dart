import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:sfm_app/core/constants/api_constants.dart';
import 'package:sfm_app/features/auth/data/datasources/auth_local_datasource.dart';
import 'package:sfm_app/features/fincas/domain/entities/finca.dart';
import 'package:sfm_app/features/fincas/domain/repositories/finca_repository.dart';

class FincaRepositoryImpl implements FincaRepository {
  final http.Client client;
  final AuthLocalDataSource authLocalDataSource;

  FincaRepositoryImpl({required this.client, required this.authLocalDataSource});

  @override
  Future<List<Finca>> getFincas() async {
    final token = await authLocalDataSource.getToken();
    if (token == null) {
      throw Exception('Token no encontrado, usuario no autenticado.');
    }

    final response = await client.get(
      Uri.parse('${ApiConstants.baseUrl}/fincas'), // Asume este endpoint
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
    );

    if (response.statusCode == 200) {
      final List<dynamic> decodedData = json.decode(response.body);
      // Asumiendo que la API devuelve directamente una lista de fincas
      return decodedData.map((json) => Finca.fromJson(json)).toList();
    } else {
      throw Exception('Error al obtener fincas: ${response.statusCode} ${response.body}');
    }
  }
} 