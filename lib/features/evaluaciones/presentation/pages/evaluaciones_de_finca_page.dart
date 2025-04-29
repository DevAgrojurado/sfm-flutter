import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:sfm_app/features/evaluaciones/domain/entities/evaluacion_general.dart';
import 'package:sfm_app/features/evaluaciones/domain/entities/evaluacion_polinizacion.dart';
import 'package:sfm_app/features/evaluaciones/presentation/bloc/evaluaciones_bloc.dart';
import 'package:sfm_app/features/evaluaciones/presentation/bloc/evaluaciones_event.dart';
import 'package:sfm_app/features/evaluaciones/presentation/bloc/evaluaciones_state.dart';
import 'package:sfm_app/main.dart'; // Importar main para colores

// Renombrar clase y añadir fincaId
class EvaluacionesDeFincaPage extends StatefulWidget {
  final int fincaId;
  final String fincaNombre;

  const EvaluacionesDeFincaPage({
    super.key, 
    required this.fincaId, 
    required this.fincaNombre
  });

  @override
  State<EvaluacionesDeFincaPage> createState() => _EvaluacionesDeFincaPageState();
}

class _EvaluacionesDeFincaPageState extends State<EvaluacionesDeFincaPage> {

  @override
  void initState() {
    super.initState();
    // Pedir las evaluaciones para la finca específica al iniciar la página
    context.read<EvaluacionesBloc>().add(FetchEvaluacionesPorFinca(widget.fincaId));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.fincaNombre),
      ),
      body: BlocBuilder<EvaluacionesBloc, EvaluacionesState>(
        builder: (context, state) {
          if (state.status == EvaluacionesStatus.initial || state.status == EvaluacionesStatus.loading) {
            return const Center(child: CircularProgressIndicator(color: Colors.green));
          }
          
          if (state.status == EvaluacionesStatus.failure) {
             return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.error_outline, size: 48, color: Colors.red),
                  const SizedBox(height: 16),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 32.0),
                    child: Text(
                      'Error al cargar evaluaciones: ${state.errorMessage}',
                      style: const TextStyle(color: Colors.red),
                      textAlign: TextAlign.center,
                    ),
                  ),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: () {
                      // Reintentar la carga para esta finca
                      context.read<EvaluacionesBloc>().add(FetchEvaluacionesPorFinca(widget.fincaId));
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.green,
                      foregroundColor: Colors.white,
                    ),
                    child: const Text('Reintentar'),
                  ),
                ],
              ),
            );
          }
  
          // Usar la nueva lista agrupada
          if (state.evaluacionesAgrupadas.isEmpty) {
            return const Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.inbox_outlined, size: 48, color: Colors.grey),
                  SizedBox(height: 16),
                  Text(
                    'No hay evaluaciones para esta finca.',
                    style: TextStyle(color: Colors.grey),
                    textAlign: TextAlign.center,
                  ),
                ],
              ),
            );
          }
  
          // Construir la lista jerárquica
          return RefreshIndicator(
            onRefresh: () async {
              context.read<EvaluacionesBloc>().add(FetchEvaluacionesPorFinca(widget.fincaId));
            },
            color: Colors.green,
            child: ListView.builder(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 8), // Ajustar padding
              itemCount: state.evaluacionesAgrupadas.length,
              itemBuilder: (context, yearIndex) {
                final yearGroup = state.evaluacionesAgrupadas[yearIndex];
                return _buildYearSection(context, yearGroup);
              },
            ),
          );
        },
      ),
    );
  }

  // --- Widgets para construir la jerarquía ---

  // Widget para la sección del Año
  Widget _buildYearSection(BuildContext context, YearGroup yearGroup) {
    return Card(
      margin: const EdgeInsets.symmetric(vertical: 6.0, horizontal: 8.0),
      clipBehavior: Clip.antiAlias, 
      child: ExpansionTile(
        key: PageStorageKey('year_${yearGroup.year}'), 
        initiallyExpanded: true, 
        leading: Icon(Icons.calendar_today_outlined, color: Theme.of(context).colorScheme.primary),
        title: Text(
          'Año ${yearGroup.year}',
          style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.w600),
        ),
        // Iterar sobre weekGroups y llamar a _buildWeekSection
        children: yearGroup.weekGroups.map((weekGroup) {
          return _buildWeekSection(context, weekGroup);
        }).toList(),
      ),
    );
  }

  // NUEVO: Widget para la sección de Semana
  Widget _buildWeekSection(BuildContext context, WeekGroup weekGroup) {
    return ExpansionTile(
      key: PageStorageKey('year_${weekGroup.weekNumber}'), // Usar weekNumber para la key
      // Ajustar padding para nivel 2
      tilePadding: const EdgeInsets.only(left: 24, right: 16, top: 4, bottom: 4),
      leading: Icon(Icons.calendar_view_week_outlined, size: 20, color: Theme.of(context).listTileTheme.iconColor), // Icono de semana
      title: Text(
        'Semana ${weekGroup.weekNumber}',
        style: Theme.of(context).textTheme.bodyLarge?.copyWith(fontWeight: FontWeight.w500),
      ),
       // Ajustar padding para nivel 3 (Fecha)
      childrenPadding: const EdgeInsets.only(left: 16 + 8, bottom: 8, right: 8), 
      children: weekGroup.dateGroups.map((dateGroup) {
          return _buildDateSection(context, dateGroup);
        }).toList(),
    );
  }

  // Widget para la sección de Fecha (ahora anidado en Semana)
  Widget _buildDateSection(BuildContext context, DateGroup dateGroup) {
    return ExpansionTile(
      key: PageStorageKey('date_${dateGroup.date}'), 
      // Ajustar padding para nivel 3
      tilePadding: const EdgeInsets.only(left: 16, right: 16, top: 2, bottom: 2),
      // Quitar icono principal, ya está en Semana
      title: Text(
        'Fecha: ${dateGroup.date}',
        style: Theme.of(context).textTheme.bodyLarge?.copyWith(fontSize: 14.5),
      ),
      // Ajustar padding para nivel 4 (Operario)
      childrenPadding: const EdgeInsets.only(left: 16 + 8, bottom: 8, right: 8),
      children: dateGroup.operatorGroups.map((operatorGroup) {
        return _buildOperatorSection(context, operatorGroup);
      }).toList(),
    );
  }

  // Widget para la sección del Operario (ahora anidado en Fecha)
  Widget _buildOperatorSection(BuildContext context, OperatorGroup operatorGroup) {
    return ExpansionTile(
      key: PageStorageKey('operator_${operatorGroup.operatorName}'),
      // Ajustar padding para nivel 4
      tilePadding: const EdgeInsets.only(left: 16, right: 16, top: 0, bottom: 0),
      leading: Icon(Icons.person_outline, size: 20, color: Theme.of(context).listTileTheme.iconColor),
      title: Text(
        operatorGroup.operatorName,
        style: Theme.of(context).textTheme.bodyMedium, // Un poco más pequeño
      ),
      subtitle: Text('${operatorGroup.evaluations.length} evaluac.', style: Theme.of(context).textTheme.bodySmall),
      // Ajustar padding para contenido final (Evaluación)
      childrenPadding: const EdgeInsets.only(left: 8, bottom: 8, right: 0),
      children: operatorGroup.evaluations.map((evaluacion) {
        return _buildEvaluacionDetailsCard(context, evaluacion);
      }).toList(),
    );
  }

  // Widget para mostrar los detalles de UNA Evaluación General (anidado en Operario)
  Widget _buildEvaluacionDetailsCard(BuildContext context, EvaluacionGeneral evaluacion) {
    final String nombreLote = evaluacion.nombreLote;
    final bool tienePolinizaciones = evaluacion.evaluacionesPolinizacion != null &&
                                   evaluacion.evaluacionesPolinizacion!.isNotEmpty;

    return Card(
      // Ajustar margen para anidamiento
      margin: const EdgeInsets.only(top: 4, bottom: 4, left: 24, right: 8), 
      elevation: 0.5, // Muy poca elevación
      shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(6.0), // Bordes menos redondeados
        ),
      child: Padding(
        padding: const EdgeInsets.all(8.0), // Padding más pequeño
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Eval #${evaluacion.id} | Lote: $nombreLote | ${evaluacion.hora}', // Más info en título
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(fontSize: 12.5, fontWeight: FontWeight.w500, color: textColorPrimary),
            ),
            // Quitar Hora y Divider si ya está en título
            // const Divider(height: 12, thickness: 0.5),
            if (tienePolinizaciones)
              Padding(
                padding: const EdgeInsets.only(top: 6.0), // Espacio antes de polinización
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                     // Quitar el título "Polinización:" si se hace obvio
                    ...evaluacion.evaluacionesPolinizacion!.map((polinizacion) {
                      return _buildPolinizacionItem(context, polinizacion);
                    }),
                  ],
                ),
              )
            // Quitar el texto "Sin detalles" si se prefiere no mostrar nada
            // else
            //   Padding(...)
          ],
        ),
      ),
    );
  }

  // Widget para el item de Polinización (ya compacto)
  Widget _buildPolinizacionItem(BuildContext context, EvaluacionPolinizacion polinizacion) {
    final String nombrePolinizador = polinizacion.nombrePolinizador; 
    return Padding(
      padding: const EdgeInsets.only(bottom: 4.0, top: 2.0), // Ajustar padding vertical
      child: Column(
         crossAxisAlignment: CrossAxisAlignment.start,
         children: [
           Text(
              ' • Palma: ${polinizacion.palma} (S${polinizacion.semana}) | Pol: $nombrePolinizador',
             style: Theme.of(context).textTheme.bodyMedium?.copyWith(fontSize: 12),
            ),
            Padding(
              padding: const EdgeInsets.only(left: 15.0, top: 3.0),
              child: Wrap( 
                spacing: 5.0, 
                runSpacing: 3.0, 
                children: [
                  _buildInfoChip(context, 'Ant', polinizacion.antesis.toString()), 
                  _buildInfoChip(context, 'Pos', polinizacion.postantesis.toString()), 
                  _buildInfoChip(context, 'Inf', polinizacion.inflorescencia.toString()), 
                ],
              ),
            )
         ],
      ),
    );
  }

  // Widget para los chips de info (aún más compacto)
  Widget _buildInfoChip(BuildContext context, String label, String value) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
      decoration: BoxDecoration(
        color: accentColor.withOpacity(0.1), // Color de acento suave
        borderRadius: BorderRadius.circular(10),
      ),
      child: RichText(
         text: TextSpan(
           style: Theme.of(context).textTheme.bodySmall?.copyWith(fontSize: 10.5),
           children: [
             TextSpan(text: '$label: ', style: const TextStyle(color: textColorSecondary)),
             TextSpan(
               text: value,
               style: const TextStyle(fontWeight: FontWeight.w600, color: accentColor),
             ),
           ]
        )
      )
    );
  }
} 