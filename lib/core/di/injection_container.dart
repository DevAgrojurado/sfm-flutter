import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:get_it/get_it.dart';
import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:sfm_app/core/api/api_client.dart';
import 'package:sfm_app/core/network/network_info.dart';
import 'package:sfm_app/features/auth/data/datasources/auth_local_datasource.dart';
import 'package:sfm_app/features/auth/data/datasources/auth_remote_datasource.dart';
import 'package:sfm_app/features/auth/data/repositories/auth_repository_impl.dart';
import 'package:sfm_app/features/auth/domain/repositories/auth_repository.dart';
import 'package:sfm_app/features/auth/domain/usecases/get_current_user_usecase.dart';
import 'package:sfm_app/features/auth/domain/usecases/is_authenticated_usecase.dart';
import 'package:sfm_app/features/auth/domain/usecases/login_usecase.dart';
import 'package:sfm_app/features/auth/domain/usecases/logout_usecase.dart';
import 'package:sfm_app/features/auth/presentation/bloc/auth_bloc.dart';
import 'package:http/http.dart' as http;
import 'package:sfm_app/core/utils/secure_storage_service.dart';
import 'package:sfm_app/features/evaluaciones/data/repositories/evaluaciones_repository_impl.dart';
import 'package:sfm_app/features/evaluaciones/domain/repositories/evaluaciones_repository.dart';
import 'package:sfm_app/features/evaluaciones/domain/usecases/get_evaluaciones.dart';
import 'package:sfm_app/features/evaluaciones/presentation/bloc/evaluaciones_bloc.dart';
import 'package:sfm_app/features/fincas/data/repositories/finca_repository_impl.dart';
import 'package:sfm_app/features/fincas/domain/repositories/finca_repository.dart';
import 'package:sfm_app/features/fincas/domain/usecases/get_fincas.dart';
import 'package:sfm_app/features/fincas/presentation/bloc/finca_bloc.dart';

final sl = GetIt.instance;

Future<void> init() async {
  // Blocs
  sl.registerFactory(
    () => AuthBloc(
      loginUseCase: sl(),
      logoutUseCase: sl(),
      getCurrentUserUseCase: sl(),
      isAuthenticatedUseCase: sl(),
    ),
  );

  sl.registerFactory(() => EvaluacionesBloc(
    getEvaluacionesGenerales: sl(),
    getEvaluacionesPorFinca: sl(),
  ));

  sl.registerFactory(() => FincaBloc(getFincas: sl()));

  // Use cases
  sl.registerLazySingleton(() => LoginUseCase(sl()));
  sl.registerLazySingleton(() => LogoutUseCase(sl()));
  sl.registerLazySingleton(() => GetCurrentUserUseCase(sl()));
  sl.registerLazySingleton(() => IsAuthenticatedUseCase(sl()));
  sl.registerLazySingleton(() => GetEvaluacionesGenerales(sl()));
  sl.registerLazySingleton(() => GetEvaluacionesPorFinca(sl()));
  sl.registerLazySingleton(() => GetEvaluacionesPolinizacion(sl()));
  sl.registerLazySingleton(() => GetEvaluacionesPolinizacionPorEvaluacionGeneral(sl()));
  sl.registerLazySingleton(() => GetFincas(sl()));

  // Repositories
  sl.registerLazySingleton<AuthRepository>(
    () => AuthRepositoryImpl(
      remoteDataSource: sl(),
      localDataSource: sl(),
      networkInfo: sl(),
    ),
  );

  sl.registerLazySingleton<EvaluacionesRepository>(() => EvaluacionesRepositoryImpl(
    client: sl(),
    authLocalDataSource: sl(),
  ));

  sl.registerLazySingleton<FincaRepository>(() => FincaRepositoryImpl(
    client: sl(),
    authLocalDataSource: sl(),
  ));

  // Data sources
  sl.registerLazySingleton<AuthRemoteDataSource>(
    () => AuthRemoteDataSourceImpl(apiClient: sl()),
  );
  sl.registerLazySingleton<AuthLocalDataSource>(
    () => AuthLocalDataSourceImpl(secureStorage: sl()),
  );

  // Core
  sl.registerLazySingleton<NetworkInfo>(() => NetworkInfoImpl(sl()));

  // External
  sl.registerLazySingleton(() => ApiClient());
  sl.registerLazySingleton(() => const FlutterSecureStorage());
  sl.registerLazySingleton(() => Connectivity());
  sl.registerLazySingleton(() => http.Client());
} 