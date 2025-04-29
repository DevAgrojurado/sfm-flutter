import 'package:dartz/dartz.dart';
import 'package:sfm_app/core/error/failures.dart';
import 'package:sfm_app/features/auth/domain/repositories/auth_repository.dart';

class LogoutUseCase {
  final AuthRepository repository;

  LogoutUseCase(this.repository);

  Future<Either<Failure, bool>> call() async {
    return await repository.logout();
  }
} 