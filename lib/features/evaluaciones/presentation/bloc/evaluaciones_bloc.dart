import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:flutter/foundation.dart';
import 'package:intl/intl.dart';
import 'package:sfm_app/features/evaluaciones/domain/entities/evaluacion_general.dart';
import 'package:sfm_app/features/evaluaciones/domain/usecases/get_evaluaciones.dart';
import 'package:sfm_app/features/evaluaciones/presentation/bloc/evaluaciones_event.dart';
import 'package:sfm_app/features/evaluaciones/presentation/bloc/evaluaciones_state.dart';

// Función auxiliar para la agrupación jerárquica (para compute)
List<YearGroup> _groupEvaluacionesHierarchically(List<EvaluacionGeneral> evaluaciones) {
  if (evaluaciones.isEmpty) return [];

  final DateFormat displayFormatter = DateFormat('dd/MM/yyyy');
  final DateFormat apiDateParser = DateFormat('dd/MM/yyyy');

  // 1. Ordenar por fecha descendente (año -> semana -> día) y luego por operario
  evaluaciones.sort((a, b) {
    try {
      DateTime dateA = apiDateParser.parse(a.fecha);
      DateTime dateB = apiDateParser.parse(b.fecha);
      // Comparar año
      int yearComparison = dateB.year.compareTo(dateA.year);
      if (yearComparison != 0) return yearComparison;
      // Comparar semana (asumiendo que `semana` en el objeto es correcto)
      int weekComparison = b.semana.compareTo(a.semana);
      if (weekComparison != 0) return weekComparison;
      // Comparar día dentro de la misma semana/año
      int dateComparison = dateB.compareTo(dateA); 
      if (dateComparison != 0) return dateComparison;
    } catch (e) {
      return 0; 
    }
    return a.nombrePolinizador.compareTo(b.nombrePolinizador); 
  });

  // 2. Agrupar (Año -> Semana -> Fecha -> Operario)
  final Map<int, Map<int, Map<String, Map<String, List<EvaluacionGeneral>>>>> grouped = {};

  for (final eval in evaluaciones) {
    try {
      final DateTime parsedDate = apiDateParser.parse(eval.fecha);
      final int year = parsedDate.year;
      final int week = eval.semana; // Usar el campo semana directamente
      final String formattedDisplayDate = displayFormatter.format(parsedDate);
      final String operatorName = eval.nombrePolinizador;

      // Inicializar estructuras anidadas
      grouped.putIfAbsent(year, () => {});
      grouped[year]!.putIfAbsent(week, () => {});
      grouped[year]![week]!.putIfAbsent(formattedDisplayDate, () => {});
      grouped[year]![week]![formattedDisplayDate]!.putIfAbsent(operatorName, () => []).add(eval);

    } catch (e) {
      print('Error parsing date or grouping eval ${eval.id}: $e'); 
    }
  }

  // 3. Convertir el Map anidado a la lista de objetos YearGroup -> WeekGroup -> ...
  final List<YearGroup> result = [];
  grouped.entries.forEach((yearEntry) { // Nivel Año
    final List<WeekGroup> weekGroups = [];
    yearEntry.value.entries.forEach((weekEntry) { // Nivel Semana
      final List<DateGroup> dateGroups = [];
      weekEntry.value.entries.forEach((dateEntry) { // Nivel Fecha
        final List<OperatorGroup> operatorGroups = [];
        dateEntry.value.entries.forEach((operatorEntry) { // Nivel Operario
          operatorGroups.add(OperatorGroup(operatorName: operatorEntry.key, evaluations: operatorEntry.value));
        });
        // Ordenar operarios si se desea
        // operatorGroups.sort((a, b) => a.operatorName.compareTo(b.operatorName));
        dateGroups.add(DateGroup(date: dateEntry.key, operatorGroups: operatorGroups));
      });
      // Ordenar fechas dentro de la semana (ya debería estar por el sort inicial)
      // dateGroups.sort((a, b) => displayFormatter.parse(b.date).compareTo(displayFormatter.parse(a.date)));
      weekGroups.add(WeekGroup(weekNumber: weekEntry.key, dateGroups: dateGroups));
    });
     // Ordenar semanas dentro del año (ya debería estar por el sort inicial)
    // weekGroups.sort((a, b) => b.weekNumber.compareTo(a.weekNumber));
    result.add(YearGroup(year: yearEntry.key, weekGroups: weekGroups));
  });
  // Ordenar años (ya debería estar por el sort inicial)
  // result.sort((a, b) => b.year.compareTo(a.year));
  return result;
}

class EvaluacionesBloc extends Bloc<EvaluacionesEvent, EvaluacionesState> {
  final GetEvaluacionesGenerales getEvaluacionesGenerales;
  final GetEvaluacionesPorFinca getEvaluacionesPorFinca;

  EvaluacionesBloc({
    required this.getEvaluacionesGenerales,
    required this.getEvaluacionesPorFinca,
  }) : super(const EvaluacionesState()) {
    on<FetchEvaluaciones>(_onFetchEvaluaciones);
    on<FetchEvaluacionesPorFinca>(_onFetchEvaluacionesPorFinca);
  }

  Future<void> _onFetchEvaluaciones(
    FetchEvaluaciones event,
    Emitter<EvaluacionesState> emit,
  ) async {
    emit(state.copyWith(status: EvaluacionesStatus.loading));
    
    try {
      final evaluaciones = await getEvaluacionesGenerales();
      emit(state.copyWith(
        status: EvaluacionesStatus.success,
        evaluaciones: evaluaciones,
        evaluacionesAgrupadas: [],
      ));
    } catch (e) {
      emit(state.copyWith(
        status: EvaluacionesStatus.failure,
        errorMessage: e.toString(),
      ));
    }
  }

  Future<void> _onFetchEvaluacionesPorFinca(
    FetchEvaluacionesPorFinca event,
    Emitter<EvaluacionesState> emit,
  ) async {
    emit(state.copyWith(status: EvaluacionesStatus.loading, evaluacionesAgrupadas: []));
    
    try {
      final evaluaciones = await getEvaluacionesPorFinca(event.fincaId);
      
      if (evaluaciones.isEmpty) {
         emit(state.copyWith(status: EvaluacionesStatus.success, evaluaciones: [], evaluacionesAgrupadas: []));
         return;
      }

      final List<YearGroup> evaluacionesAgrupadas = await compute(_groupEvaluacionesHierarchically, evaluaciones);
      
      emit(state.copyWith(
        status: EvaluacionesStatus.success,
        evaluaciones: evaluaciones, 
        evaluacionesAgrupadas: evaluacionesAgrupadas,
      ));
    } catch (e) {
      emit(state.copyWith(
        status: EvaluacionesStatus.failure,
        errorMessage: e.toString(),
      ));
    }
  }
} 