import 'package:dartz/dartz.dart';
import 'package:sfm_app/core/error/failures.dart';
import 'package:sfm_app/features/auth/domain/entities/user.dart';
import 'package:sfm_app/features/auth/domain/repositories/auth_repository.dart';

class GetCurrentUserUseCase {
  final AuthRepository repository;

  GetCurrentUserUseCase(this.repository);

  Future<Either<Failure, User?>> call() async {
    return await repository.getCurrentUser();
  }
} 