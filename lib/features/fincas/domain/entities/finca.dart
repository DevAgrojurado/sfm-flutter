import 'package:equatable/equatable.dart';

class Finca extends Equatable {
  final int id;
  final String nombre;
  // Añade otros campos relevantes de la finca si los necesitas

  const Finca({required this.id, required this.nombre});

  factory Finca.fromJson(Map<String, dynamic> json) {
    return Finca(
      id: json['id'],
      nombre: json['descripcion']?.toString() ?? 'Descripción no disponible',
    );
  }

  @override
  List<Object?> get props => [id, nombre];
} 