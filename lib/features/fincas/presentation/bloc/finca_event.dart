import 'package:equatable/equatable.dart';

abstract class FincaEvent extends Equatable {
  const FincaEvent();

  @override
  List<Object?> get props => [];
}

class FetchFincas extends FincaEvent {
  const FetchFincas();
} 