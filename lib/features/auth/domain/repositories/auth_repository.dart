import 'package:dartz/dartz.dart';
import 'package:sfm_app/core/error/failures.dart';
import 'package:sfm_app/features/auth/domain/entities/user.dart';

abstract class AuthRepository {
  /// Autentica al usuario con email y contraseña
  Future<Either<Failure, User>> login(String email, String password);
  
  /// Cierra la sesión del usuario actual
  Future<Either<Failure, bool>> logout();
  
  /// Obtiene el usuario actualmente autenticado
  Future<Either<Failure, User?>> getCurrentUser();
  
  /// Verifica si hay un usuario autenticado
  Future<Either<Failure, bool>> isAuthenticated();
} 