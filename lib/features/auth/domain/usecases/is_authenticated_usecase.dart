import 'package:dartz/dartz.dart';
import 'package:sfm_app/core/error/failures.dart';
import 'package:sfm_app/features/auth/domain/repositories/auth_repository.dart';

class IsAuthenticatedUseCase {
  final AuthRepository repository;

  IsAuthenticatedUseCase(this.repository);

  Future<Either<Failure, bool>> call() async {
    return await repository.isAuthenticated();
  }
} 