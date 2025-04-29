import 'package:equatable/equatable.dart';
import 'package:sfm_app/features/auth/domain/entities/user.dart';

enum AuthStatus {
  initial,
  loading,
  authenticated,
  unauthenticated,
  error,
}

class AuthState extends Equatable {
  final AuthStatus status;
  final User? user;
  final String? errorMessage;

  const AuthState({
    this.status = AuthStatus.initial,
    this.user,
    this.errorMessage,
  });

  // Estados preconfigurados
  factory AuthState.initial() => const AuthState();
  
  factory AuthState.loading() => const AuthState(status: AuthStatus.loading);
  
  factory AuthState.authenticated(User user) => AuthState(
        status: AuthStatus.authenticated,
        user: user,
      );
  
  factory AuthState.unauthenticated() => const AuthState(
        status: AuthStatus.unauthenticated,
      );
  
  factory AuthState.error(String message) => AuthState(
        status: AuthStatus.error,
        errorMessage: message,
      );

  // Helper para verificar si está autenticado
  bool get isAuthenticated => status == AuthStatus.authenticated;

  // Copiar estado con cambios
  AuthState copyWith({
    AuthStatus? status,
    User? user,
    String? errorMessage,
  }) {
    return AuthState(
      status: status ?? this.status,
      user: user ?? this.user,
      errorMessage: errorMessage ?? this.errorMessage,
    );
  }

  @override
  List<Object?> get props => [status, user, errorMessage];
} 