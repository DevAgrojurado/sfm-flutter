import 'package:flutter/foundation.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:sfm_app/features/auth/domain/usecases/get_current_user_usecase.dart';
import 'package:sfm_app/features/auth/domain/usecases/is_authenticated_usecase.dart';
import 'package:sfm_app/features/auth/domain/usecases/login_usecase.dart';
import 'package:sfm_app/features/auth/domain/usecases/logout_usecase.dart';
import 'package:sfm_app/features/auth/presentation/bloc/auth_event.dart';
import 'package:sfm_app/features/auth/presentation/bloc/auth_state.dart';

class AuthBloc extends Bloc<AuthEvent, AuthState> {
  final LoginUseCase loginUseCase;
  final LogoutUseCase logoutUseCase;
  final GetCurrentUserUseCase getCurrentUserUseCase;
  final IsAuthenticatedUseCase isAuthenticatedUseCase;

  AuthBloc({
    required this.loginUseCase,
    required this.logoutUseCase,
    required this.getCurrentUserUseCase,
    required this.isAuthenticatedUseCase,
  }) : super(AuthState.initial()) {
    on<CheckAuthStatusEvent>(_onCheckAuthStatus);
    on<LoginEvent>(_onLogin);
    on<LogoutEvent>(_onLogout);
  }

  Future<void> _onCheckAuthStatus(
    CheckAuthStatusEvent event,
    Emitter<AuthState> emit,
  ) async {
    if (kDebugMode) {
      print('CheckAuthStatusEvent: Verificando estado de autenticación');
    }
    emit(AuthState.loading());
    
    final isAuthResult = await isAuthenticatedUseCase();
    
    await isAuthResult.fold(
      (failure) async {
        if (kDebugMode) {
          print('Error al verificar autenticación: ${failure.message}');
        }
        emit(AuthState.unauthenticated());
      },
      (isAuthenticated) async {
        if (kDebugMode) {
          print('¿Usuario autenticado?: $isAuthenticated');
        }
        if (isAuthenticated) {
          final userResult = await getCurrentUserUseCase();
          await userResult.fold(
            (failure) {
              if (kDebugMode) {
                print('Error al obtener usuario: ${failure.message}');
              }
              emit(AuthState.unauthenticated());
            },
            (user) {
              if (user != null) {
                if (kDebugMode) {
                  print('Usuario obtenido correctamente: ${user.nombre}');
                }
                emit(AuthState.authenticated(user));
              } else {
                if (kDebugMode) {
                  print('No se encontró usuario almacenado');
                }
                emit(AuthState.unauthenticated());
              }
            },
          );
        } else {
          emit(AuthState.unauthenticated());
        }
      },
    );
  }

  Future<void> _onLogin(
    LoginEvent event,
    Emitter<AuthState> emit,
  ) async {
    if (kDebugMode) {
      print('LoginEvent: Intentando iniciar sesión con email: ${event.email}');
    }
    emit(AuthState.loading());
    
    final result = await loginUseCase(
      LoginParams(
        email: event.email,
        password: event.password,
      ),
    );
    
    result.fold(
      (failure) {
        if (kDebugMode) {
          print('Error al iniciar sesión: ${failure.message}');
        }
        emit(AuthState.error(failure.message));
      },
      (user) {
        if (kDebugMode) {
          print('Login exitoso para: ${user.nombre}, rol: ${user.tipoRol}');
        }
        emit(AuthState.authenticated(user));
      },
    );
  }

  Future<void> _onLogout(
    LogoutEvent event,
    Emitter<AuthState> emit,
  ) async {
    if (kDebugMode) {
      print('LogoutEvent: Cerrando sesión');
    }
    emit(AuthState.loading());
    
    final result = await logoutUseCase();
    
    result.fold(
      (failure) {
        if (kDebugMode) {
          print('Error al cerrar sesión: ${failure.message}');
        }
        emit(AuthState.error(failure.message));
      },
      (_) {
        if (kDebugMode) {
          print('Sesión cerrada exitosamente');
        }
        emit(AuthState.unauthenticated());
      },
    );
  }
} 