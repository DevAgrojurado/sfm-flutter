import 'package:equatable/equatable.dart';
import 'package:sfm_app/features/fincas/domain/entities/finca.dart';

enum FincaStatus { initial, loading, success, failure }

class FincaState extends Equatable {
  final FincaStatus status;
  final List<Finca> fincas;
  final String errorMessage;

  const FincaState({
    this.status = FincaStatus.initial,
    this.fincas = const [],
    this.errorMessage = '',
  });

  FincaState copyWith({
    FincaStatus? status,
    List<Finca>? fincas,
    String? errorMessage,
  }) {
    return FincaState(
      status: status ?? this.status,
      fincas: fincas ?? this.fincas,
      errorMessage: errorMessage ?? this.errorMessage,
    );
  }

  @override
  List<Object?> get props => [status, fincas, errorMessage];
} 