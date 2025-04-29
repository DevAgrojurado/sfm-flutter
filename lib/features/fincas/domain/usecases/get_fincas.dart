import 'package:sfm_app/features/fincas/domain/entities/finca.dart';
import 'package:sfm_app/features/fincas/domain/repositories/finca_repository.dart';

class GetFincas {
  final FincaRepository repository;

  GetFincas(this.repository);

  Future<List<Finca>> call() async {
    // Podrías añadir lógica aquí si fuera necesario (ej: ordenar)
    return await repository.getFincas();
  }
} 