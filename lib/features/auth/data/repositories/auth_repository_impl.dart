import 'package:dartz/dartz.dart';
import 'package:sfm_app/core/error/exceptions.dart';
import 'package:sfm_app/core/error/failures.dart';
import 'package:sfm_app/core/network/network_info.dart';
import 'package:sfm_app/features/auth/data/datasources/auth_local_datasource.dart';
import 'package:sfm_app/features/auth/data/datasources/auth_remote_datasource.dart';
import 'package:sfm_app/features/auth/domain/entities/user.dart';
import 'package:sfm_app/features/auth/domain/repositories/auth_repository.dart';

class AuthRepositoryImpl implements AuthRepository {
  final AuthRemoteDataSource remoteDataSource;
  final AuthLocalDataSource localDataSource;
  final NetworkInfo networkInfo;

  AuthRepositoryImpl({
    required this.remoteDataSource,
    required this.localDataSource,
    required this.networkInfo,
  });

  @override
  Future<Either<Failure, User>> login(String email, String password) async {
    if (await networkInfo.isConnected) {
      try {
        final result = await remoteDataSource.login(email, password);
        final user = result['user'];
        final token = result['token'];
        
        // Guardar datos localmente
        await localDataSource.cacheUser(user);
        await localDataSource.cacheToken(token);
        
        return Right(user);
      } on ServerException catch (e) {
        return Left(ServerFailure(message: e.message));
      } on AuthenticationException catch (e) {
        return Left(AuthenticationFailure(message: e.message));
      } on ConnectionException catch (e) {
        return Left(ConnectionFailure(message: e.message));
      } catch (e) {
        return Left(ServerFailure(message: e.toString()));
      }
    } else {
      return const Left(ConnectionFailure(message: 'Sin conexión a internet'));
    }
  }

  @override
  Future<Either<Failure, bool>> logout() async {
    try {
      final token = await localDataSource.getToken();
      
      if (token != null && await networkInfo.isConnected) {
        try {
          await remoteDataSource.logout(token);
        } catch (e) {
          // Solo registramos el error pero continuamos para limpiar localmente
        }
      }
      
      await localDataSource.clearAuthData();
      return const Right(true);
    } on CacheException catch (e) {
      return Left(CacheFailure(message: e.message));
    } catch (e) {
      return Left(ServerFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, User?>> getCurrentUser() async {
    try {
      final user = await localDataSource.getLastUser();
      return Right(user);
    } on CacheException catch (e) {
      return Left(CacheFailure(message: e.message));
    } catch (e) {
      return Left(ServerFailure(message: e.toString()));
    }
  }

  @override
  Future<Either<Failure, bool>> isAuthenticated() async {
    try {
      final token = await localDataSource.getToken();
      return Right(token != null);
    } on CacheException catch (e) {
      return Left(CacheFailure(message: e.message));
    } catch (e) {
      return Left(ServerFailure(message: e.toString()));
    }
  }
} 