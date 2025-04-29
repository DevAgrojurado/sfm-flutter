import 'package:equatable/equatable.dart';

abstract class AuthEvent extends Equatable {
  const AuthEvent();

  @override
  List<Object?> get props => [];
}

// Evento para inicializar y verificar autenticación
class CheckAuthStatusEvent extends AuthEvent {
  const CheckAuthStatusEvent();
}

// Evento para iniciar sesión
class LoginEvent extends AuthEvent {
  final String email;
  final String password;

  const LoginEvent({
    required this.email,
    required this.password,
  });

  @override
  List<Object> get props => [email, password];
}

// Evento para cerrar sesión
class LogoutEvent extends AuthEvent {
  const LogoutEvent();
} 