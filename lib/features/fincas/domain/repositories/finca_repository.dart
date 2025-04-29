import 'package:sfm_app/features/fincas/domain/entities/finca.dart';

abstract class FincaRepository {
  Future<List<Finca>> getFincas();
} 