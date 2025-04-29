class EvaluacionPolinizacion {
  final int id;
  final int evaluaciongeneralid;
  final String fecha;
  final String hora;
  final int semana;
  final String ubicacion;
  final int idevaluador;
  final int idpolinizador;
  final int idlote;
  final String seccion;
  final int palma;
  final int inflorescencia;
  final int antesis;
  final int antesisDejadas;
  final int postantesisDejadas;
  final int postantesis;
  final String espate;
  final String aplicacion;
  final String marcacion;
  final String? repaso1;
  final String? repaso2;
  final String? observaciones;
  final String timestamp;
  
  // Relaciones
  final Map<String, dynamic>? evaluacionGeneral;
  final Map<String, dynamic>? evaluador;
  final Map<String, dynamic>? polinizador;
  final Map<String, dynamic>? lote;

  EvaluacionPolinizacion({
    required this.id,
    required this.evaluaciongeneralid,
    required this.fecha,
    required this.hora,
    required this.semana,
    required this.ubicacion,
    required this.idevaluador,
    required this.idpolinizador,
    required this.idlote,
    required this.seccion,
    required this.palma,
    required this.inflorescencia,
    required this.antesis,
    required this.antesisDejadas,
    required this.postantesisDejadas,
    required this.postantesis,
    required this.espate,
    required this.aplicacion,
    required this.marcacion,
    this.repaso1,
    this.repaso2,
    this.observaciones,
    required this.timestamp,
    this.evaluacionGeneral,
    this.evaluador,
    this.polinizador,
    this.lote,
  });

  factory EvaluacionPolinizacion.fromJson(Map<String, dynamic> json) {
    // Función auxiliar para conversión segura a String
    String _asString(dynamic value) => value?.toString() ?? '';
    // Función auxiliar para conversión segura a String nullable
    String? _asNullableString(dynamic value) => value?.toString();
    // Función auxiliar para conversión segura a int
    int _asInt(dynamic value) {
      if (value is int) return value;
      if (value is String) return int.tryParse(value) ?? 0;
      return 0;
    }
    // Función auxiliar para conversión segura a int nullable
    int? _asNullableInt(dynamic value) {
       if (value == null) return null;
       if (value is int) return value;
       if (value is String) return int.tryParse(value);
       return null;
    }

    return EvaluacionPolinizacion(
      // Campos numéricos (convertir de forma segura)
      id: _asInt(json['id']),
      evaluaciongeneralid: _asInt(json['evaluaciongeneralid']),
      semana: _asInt(json['semana']),
      idevaluador: _asInt(json['idevaluador']),
      idpolinizador: _asInt(json['idpolinizador']),
      idlote: _asInt(json['idlote']),
      palma: _asInt(json['palma']),
      inflorescencia: _asInt(json['inflorescencia']),
      antesis: _asInt(json['antesis']),
      antesisDejadas: _asInt(json['antesisDejadas']),
      postantesisDejadas: _asInt(json['postantesisDejadas']),
      postantesis: _asInt(json['postantesis']),

      // Campos de texto (convertir de forma segura)
      fecha: _asString(json['fecha']),
      hora: _asString(json['hora']),
      ubicacion: _asString(json['ubicacion']),
      seccion: _asString(json['seccion']),
      espate: _asString(json['espate']),
      aplicacion: _asString(json['aplicacion']),
      marcacion: _asString(json['marcacion']),
      timestamp: _asString(json['timestamp']), 
      observaciones: _asNullableString(json['observaciones']), // Nullable
      repaso1: _asNullableString(json['repaso1']), // Nullable
      repaso2: _asNullableString(json['repaso2']), // Nullable

      // Relaciones (asumiendo que vienen como Map o null)
      evaluacionGeneral: json['evaluacionGeneral'] is Map<String, dynamic> ? json['evaluacionGeneral'] : null,
      evaluador: json['evaluador'] is Map<String, dynamic> ? json['evaluador'] : null,
      polinizador: json['polinizador'] is Map<String, dynamic> ? json['polinizador'] : null,
      lote: json['lote'] is Map<String, dynamic> ? json['lote'] : null,
    );
  }
  
  String get nombrePolinizador {
    return polinizador != null ? polinizador!['nombre'] : 'Sin polinizador';
  }
} 