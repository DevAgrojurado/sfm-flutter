import 'package:equatable/equatable.dart';

class User extends Equatable {
  final int id;
  final String? codigo;
  final String nombre;
  final String? cedula;
  final String email;
  final int? idCargo;
  final int? idArea;
  final int? idFinca;
  final String? rol;
  final String? tipoRol;
  final bool vigente;
  final Map<String, bool> permisos;

  const User({
    required this.id,
    this.codigo,
    required this.nombre,
    this.cedula,
    required this.email,
    this.idCargo,
    this.idArea,
    this.idFinca,
    this.rol,
    this.tipoRol,
    this.vigente = true,
    this.permisos = const {},
  });

  bool get puedeAgregar => permisos['puede_agregar'] ?? false;
  bool get puedeEditar => permisos['puede_editar'] ?? false;
  bool get puedeEliminar => permisos['puede_eliminar'] ?? false;
  bool get tieneAccesoTotal => permisos['acceso_total'] ?? false;

  @override
  List<Object?> get props => [
    id, 
    codigo, 
    nombre, 
    cedula, 
    email, 
    idCargo, 
    idArea, 
    idFinca, 
    rol, 
    tipoRol, 
    vigente,
    permisos
  ];
} 