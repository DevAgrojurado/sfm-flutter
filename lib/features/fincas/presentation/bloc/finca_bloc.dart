import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:sfm_app/features/fincas/domain/usecases/get_fincas.dart';
import 'package:sfm_app/features/fincas/presentation/bloc/finca_event.dart';
import 'package:sfm_app/features/fincas/presentation/bloc/finca_state.dart';

class FincaBloc extends Bloc<FincaEvent, FincaState> {
  final GetFincas getFincas;

  FincaBloc({required this.getFincas}) : super(const FincaState()) {
    on<FetchFincas>(_onFetchFincas);
  }

  Future<void> _onFetchFincas(
    FetchFincas event,
    Emitter<FincaState> emit,
  ) async {
    emit(state.copyWith(status: FincaStatus.loading));
    try {
      final fincas = await getFincas();
      emit(state.copyWith(
        status: FincaStatus.success,
        fincas: fincas,
      ));
    } catch (e) {
      emit(state.copyWith(
        status: FincaStatus.failure,
        errorMessage: e.toString(),
      ));
    }
  }
} 