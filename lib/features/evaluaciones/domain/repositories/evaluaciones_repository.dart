import 'package:sfm_app/features/evaluaciones/domain/entities/evaluacion_general.dart';
import 'package:sfm_app/features/evaluaciones/domain/entities/evaluacion_polinizacion.dart';

abstract class EvaluacionesRepository {
  Future<List<EvaluacionGeneral>> getEvaluacionesGenerales();
  Future<List<EvaluacionGeneral>> getEvaluacionesPorFinca(int fincaId);
  Future<List<EvaluacionPolinizacion>> getEvaluacionesPolinizacion();
  Future<List<EvaluacionPolinizacion>> getEvaluacionesPolinizacionPorEvaluacionGeneral(int evaluacionGeneralId);
} 