import 'package:equatable/equatable.dart';
import 'package:sfm_app/features/evaluaciones/domain/entities/evaluacion_general.dart';

// --- Modelos para la Agrupación Jerárquica ---

class OperatorGroup extends Equatable {
  final String operatorName;
  final List<EvaluacionGeneral> evaluations;

  const OperatorGroup({required this.operatorName, required this.evaluations});

  @override
  List<Object?> get props => [operatorName, evaluations];
}

class DateGroup extends Equatable {
  final String date; // Fecha formateada (ej: "dd/MM/yyyy")
  final List<OperatorGroup> operatorGroups;

  const DateGroup({required this.date, required this.operatorGroups});

  @override
  List<Object?> get props => [date, operatorGroups];
}

// NUEVO: Modelo para Grupo de Semana
class WeekGroup extends Equatable {
  final int weekNumber;
  final List<DateGroup> dateGroups;

  const WeekGroup({required this.weekNumber, required this.dateGroups});

  @override
  List<Object?> get props => [weekNumber, dateGroups];
}

class YearGroup extends Equatable {
  final int year;
  // Modificado: Ahora contiene grupos de semanas
  final List<WeekGroup> weekGroups;

  const YearGroup({required this.year, required this.weekGroups});

  @override
  List<Object?> get props => [year, weekGroups];
}

// --- Estado del BLoC ---

enum EvaluacionesStatus { initial, loading, success, failure }

class EvaluacionesState extends Equatable {
  final EvaluacionesStatus status;
  final List<EvaluacionGeneral> evaluaciones; // Lista plana original (puede ser útil)
  // Nuevo campo para datos agrupados
  final List<YearGroup> evaluacionesAgrupadas;
  final String errorMessage;

  const EvaluacionesState({
    this.status = EvaluacionesStatus.initial,
    this.evaluaciones = const [],
    this.evaluacionesAgrupadas = const [], // Inicializar vacío
    this.errorMessage = '',
  });

  EvaluacionesState copyWith({
    EvaluacionesStatus? status,
    List<EvaluacionGeneral>? evaluaciones,
    List<YearGroup>? evaluacionesAgrupadas, // Añadir al copyWith
    String? errorMessage,
  }) {
    return EvaluacionesState(
      status: status ?? this.status,
      evaluaciones: evaluaciones ?? this.evaluaciones,
      evaluacionesAgrupadas: evaluacionesAgrupadas ?? this.evaluacionesAgrupadas, // Usar nuevo valor
      errorMessage: errorMessage ?? this.errorMessage,
    );
  }

  @override
  // Añadir evaluacionesAgrupadas a props
  List<Object?> get props => [status, evaluaciones, evaluacionesAgrupadas, errorMessage];
} 