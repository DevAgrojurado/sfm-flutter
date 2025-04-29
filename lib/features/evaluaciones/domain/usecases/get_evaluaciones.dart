import 'package:sfm_app/features/evaluaciones/domain/entities/evaluacion_general.dart';
import 'package:sfm_app/features/evaluaciones/domain/entities/evaluacion_polinizacion.dart';
import 'package:sfm_app/features/evaluaciones/domain/repositories/evaluaciones_repository.dart';

class GetEvaluacionesGenerales {
  final EvaluacionesRepository repository;

  GetEvaluacionesGenerales(this.repository);

  Future<List<EvaluacionGeneral>> call() async {
    return await repository.getEvaluacionesGenerales();
  }
}

class GetEvaluacionesPorFinca {
  final EvaluacionesRepository repository;

  GetEvaluacionesPorFinca(this.repository);

  Future<List<EvaluacionGeneral>> call(int fincaId) async {
    return await repository.getEvaluacionesPorFinca(fincaId);
  }
}

class GetEvaluacionesPolinizacion {
  final EvaluacionesRepository repository;

  GetEvaluacionesPolinizacion(this.repository);

  Future<List<EvaluacionPolinizacion>> call() async {
    return await repository.getEvaluacionesPolinizacion();
  }
}

class GetEvaluacionesPolinizacionPorEvaluacionGeneral {
  final EvaluacionesRepository repository;

  GetEvaluacionesPolinizacionPorEvaluacionGeneral(this.repository);

  Future<List<EvaluacionPolinizacion>> call(int evaluacionGeneralId) async {
    return await repository.getEvaluacionesPolinizacionPorEvaluacionGeneral(evaluacionGeneralId);
  }
} 