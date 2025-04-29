import 'package:dartz/dartz.dart';
import 'package:equatable/equatable.dart';
import 'package:sfm_app/core/error/failures.dart';
import 'package:sfm_app/features/auth/domain/entities/user.dart';
import 'package:sfm_app/features/auth/domain/repositories/auth_repository.dart';

class LoginUseCase {
  final AuthRepository repository;

  LoginUseCase(this.repository);

  Future<Either<Failure, User>> call(LoginParams params) async {
    return await repository.login(params.email, params.password);
  }
}

class LoginParams extends Equatable {
  final String email;
  final String password;

  const LoginParams({
    required this.email,
    required this.password,
  });

  @override
  List<Object> get props => [email, password];
} 