import 'package:flutter/foundation.dart';
import 'package:sfm_app/features/auth/domain/entities/user.dart';

class UserModel extends User {
  const UserModel({
    required super.id,
    super.codigo,
    required super.nombre,
    super.cedula,
    required super.email,
    super.idCargo,
    super.idArea,
    super.idFinca,
    super.rol,
    super.tipoRol,
    super.vigente = true,
    super.permisos = const {},
  });

  factory UserModel.fromJson(Map<String, dynamic> json, {Map<String, dynamic>? permisosData}) {
    if (kDebugMode) {
      print('Datos del usuario recibidos: $json');
      print('Datos de permisos recibidos: $permisosData');
    }

    Map<String, bool> permisos = {};
    
    if (permisosData != null) {
      try {
        permisosData.forEach((key, value) {
          if (value is bool) {
            permisos[key] = value;
          } else if (value is int) {
            permisos[key] = value == 1;
          } else if (value is String) {
            permisos[key] = value.toLowerCase() == 'true' || value == '1';
          }
        });
      } catch (e) {
        if (kDebugMode) {
          print('Error al procesar permisos: $e');
        }
      }
    }

    try {
      return UserModel(
        id: json['id'] is String ? int.parse(json['id']) : json['id'],
        codigo: json['codigo'],
        nombre: json['nombre'],
        cedula: json['cedula'],
        email: json['email'],
        idCargo: json['idCargo'] is String && json['idCargo'].isNotEmpty ? int.parse(json['idCargo']) : json['idCargo'],
        idArea: json['idArea'] is String && json['idArea'].isNotEmpty ? int.parse(json['idArea']) : json['idArea'],
        idFinca: json['idFinca'] is String && json['idFinca'].isNotEmpty ? int.parse(json['idFinca']) : json['idFinca'],
        rol: json['rol'],
        tipoRol: json['tipo_rol'],
        vigente: json['vigente'] == 1 || json['vigente'] == true || json['vigente'] == '1',
        permisos: permisos,
      );
    } catch (e) {
      if (kDebugMode) {
        print('Error al parsear usuario: $e');
      }
      // Si hay un error, intentar con una estructura más tolerante
      return UserModel(
        id: _parseId(json['id']),
        codigo: json['codigo']?.toString(),
        nombre: json['nombre'] ?? 'Usuario',
        cedula: json['cedula']?.toString(),
        email: json['email'] ?? 'sin@email.com',
        idCargo: _parseIntOrNull(json['idCargo']),
        idArea: _parseIntOrNull(json['idArea']),
        idFinca: _parseIntOrNull(json['idFinca']),
        rol: json['rol']?.toString(),
        tipoRol: json['tipo_rol']?.toString(),
        vigente: json['vigente'] == 1 || json['vigente'] == true || json['vigente'] == '1',
        permisos: permisos,
      );
    }
  }

  static int _parseId(dynamic value) {
    if (value == null) return -1;
    if (value is int) return value;
    if (value is String) {
      try {
        return int.parse(value);
      } catch (_) {
        return -1;
      }
    }
    return -1;
  }

  static int? _parseIntOrNull(dynamic value) {
    if (value == null) return null;
    if (value is int) return value;
    if (value is String && value.isNotEmpty) {
      try {
        return int.parse(value);
      } catch (_) {
        return null;
      }
    }
    return null;
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'codigo': codigo,
      'nombre': nombre,
      'cedula': cedula,
      'email': email,
      'idCargo': idCargo,
      'idArea': idArea,
      'idFinca': idFinca,
      'rol': rol,
      'tipo_rol': tipoRol,
      'vigente': vigente ? 1 : 0,
    };
  }
} 