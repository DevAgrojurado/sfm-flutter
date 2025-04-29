import 'package:sfm_app/features/evaluaciones/domain/entities/evaluacion_polinizacion.dart';
import 'package:equatable/equatable.dart';

class EvaluacionGeneral extends Equatable {
  final int id;
  final String fecha;
  final String hora;
  final int semana;
  final int? idevaluadorev;
  final int? idpolinizadorev;
  final int? idloteev;
  final String? fotopath;
  final String? firmapath;
  final String timestamp;
  
  final Map<String, dynamic>? evaluador;
  final Map<String, dynamic>? polinizador;
  final Map<String, dynamic>? lote;
  final Map<String, dynamic>? finca;
  final int? seccion;
  final dynamic calificacion;
  final String? observaciones;
  final List<EvaluacionPolinizacion>? evaluacionesPolinizacion;

  const EvaluacionGeneral({
    required this.id,
    required this.fecha,
    required this.hora,
    required this.semana,
    this.evaluador,
    this.polinizador,
    this.lote,
    this.finca,
    this.seccion,
    this.calificacion,
    this.observaciones,
    this.idevaluadorev,
    this.idpolinizadorev,
    this.idloteev,
    this.fotopath,
    this.firmapath,
    required this.timestamp,
    this.evaluacionesPolinizacion,
  });

  factory EvaluacionGeneral.fromJson(Map<String, dynamic> json) {
    List<EvaluacionPolinizacion>? evaluacionesPolinizacion;
    
    if (json['evaluacionesPolinizacion'] != null && json['evaluacionesPolinizacion'] is List) {
      try {
        evaluacionesPolinizacion = List<EvaluacionPolinizacion>.from(
          (json['evaluacionesPolinizacion'] as List)
              .map((x) => x is Map<String, dynamic> ? EvaluacionPolinizacion.fromJson(x) : null)
              .where((x) => x != null)
              .cast<EvaluacionPolinizacion>()
        );
      } catch (e) {
        print("Error parsing evaluacionesPolinizacion list: $e");
        evaluacionesPolinizacion = [];
      }
    }

    String _asString(dynamic value) => value?.toString() ?? '';
    String? _asNullableString(dynamic value) => value?.toString();
    int _asInt(dynamic value) {
      if (value is int) return value;
      if (value is String) return int.tryParse(value) ?? 0;
      if (value is double) return value.toInt();
      return 0;
    }
    int? _asNullableInt(dynamic value) {
      if (value == null) return null;
      if (value is int) return value;
      if (value is String) return int.tryParse(value);
      if (value is double) return value.toInt();
      return null;
    }
    Map<String, dynamic>? _asNullableMap(dynamic value) {
      return value is Map<String, dynamic> ? value : null;
    }

    return EvaluacionGeneral(
      id: _asInt(json['id']),
      semana: _asInt(json['semana']),
      fecha: _asString(json['fecha']),
      hora: _asString(json['hora']),
      timestamp: _asString(json['timestamp']),
      
      evaluador: _asNullableMap(json['evaluador']),
      polinizador: _asNullableMap(json['polinizador']),
      lote: _asNullableMap(json['lote']),
      finca: _asNullableMap(json['finca']),

      seccion: _asNullableInt(json['seccion']),
      calificacion: json['calificacion'],
      observaciones: _asNullableString(json['observaciones']),

      idevaluadorev: _asNullableInt(json['idevaluadorev']),
      idpolinizadorev: _asNullableInt(json['idpolinizadorev']),
      idloteev: _asNullableInt(json['idloteev']),
      
      fotopath: _asNullableString(json['fotopath']),
      firmapath: _asNullableString(json['firmapath']),
      
      evaluacionesPolinizacion: evaluacionesPolinizacion,
    );
  }

  String get nombreFinca {
    return finca?['descripcion']?.toString() ?? 'Sin finca';
  }
  
  String get nombreLote {
    return lote?['descripcion']?.toString() ?? 'Sin lote';
  }
  
  String get nombrePolinizador {
    return polinizador?['nombre']?.toString() ?? 'Sin polinizador';
  }
  
  String get nombreEvaluador {
    return evaluador?['nombre']?.toString() ?? 'Sin evaluador';
  }

  @override
  List<Object?> get props => [
        id, fecha, hora, semana, evaluador, polinizador, lote, finca, 
        seccion, calificacion, observaciones, idevaluadorev, idpolinizadorev, idloteev,
        fotopath, firmapath, timestamp, evaluacionesPolinizacion
      ];
} 