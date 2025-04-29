import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:sfm_app/features/evaluaciones/presentation/pages/evaluaciones_de_finca_page.dart'; // Importar la página de destino
import 'package:sfm_app/features/fincas/presentation/bloc/finca_bloc.dart';
import 'package:sfm_app/features/fincas/presentation/bloc/finca_event.dart';
import 'package:sfm_app/features/fincas/presentation/bloc/finca_state.dart';

class SeleccionarFincaPage extends StatefulWidget {
  const SeleccionarFincaPage({super.key});

  @override
  State<SeleccionarFincaPage> createState() => _SeleccionarFincaPageState();
}

class _SeleccionarFincaPageState extends State<SeleccionarFincaPage> {
  @override
  void initState() {
    super.initState();
    // Solicitar la lista de fincas al entrar en la página
    context.read<FincaBloc>().add(const FetchFincas());
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Seleccionar Finca'),
        backgroundColor: Colors.white,
        foregroundColor: Colors.green,
        elevation: 1,
      ),
      body: BlocBuilder<FincaBloc, FincaState>(
        builder: (context, state) {
          if (state.status == FincaStatus.loading || state.status == FincaStatus.initial) {
            return const Center(child: CircularProgressIndicator(color: Colors.green));
          }

          if (state.status == FincaStatus.failure) {
            return Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(Icons.error_outline, color: Colors.red, size: 48),
                  const SizedBox(height: 16),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 32.0),
                    child: Text(
                      'Error al cargar fincas: ${state.errorMessage}',
                      style: const TextStyle(color: Colors.red),
                      textAlign: TextAlign.center,
                    ),
                  ),
                  const SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: () {
                      context.read<FincaBloc>().add(const FetchFincas());
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

          if (state.fincas.isEmpty) {
            return const Center(
              child: Text('No hay fincas disponibles.', style: TextStyle(color: Colors.grey)),
            );
          }

          // Lista de fincas
          return ListView.separated(
            itemCount: state.fincas.length,
            separatorBuilder: (context, index) => const Divider(height: 1, indent: 16, endIndent: 16),
            itemBuilder: (context, index) {
              final finca = state.fincas[index];
              return ListTile(
                leading: const Icon(Icons.landscape_outlined, color: Colors.green),
                title: Text(finca.nombre),
                trailing: const Icon(Icons.chevron_right, color: Colors.grey),
                onTap: () {
                  // Navegar pasando ID y Nombre
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => EvaluacionesDeFincaPage(
                        fincaId: finca.id,
                        fincaNombre: finca.nombre,
                      ),
                    ),
                  );
                },
              );
            },
          );
        },
      ),
    );
  }
} 