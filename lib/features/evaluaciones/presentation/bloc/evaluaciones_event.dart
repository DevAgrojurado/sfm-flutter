import 'package:equatable/equatable.dart';

abstract class EvaluacionesEvent extends Equatable {
  const EvaluacionesEvent();

  @override
  List<Object?> get props => [];
}

class FetchEvaluaciones extends EvaluacionesEvent {
  const FetchEvaluaciones();
}

class FetchEvaluacionesPorFinca extends EvaluacionesEvent {
  final int fincaId;

  const FetchEvaluacionesPorFinca(this.fincaId);

  @override
  List<Object?> get props => [fincaId];
}

class FetchAllEvaluacionesAgrupadas extends EvaluacionesEvent {
  const FetchAllEvaluacionesAgrupadas();
} 