abstract class PlayerCubitState {}

class InitialState extends PlayerCubitState {}

class StoppedState extends PlayerCubitState {
  final bool isConnected;

  StoppedState({required this.isConnected});
}

class LoadingState extends PlayerCubitState {}

class PlayingState extends PlayerCubitState {}

class FailureState extends PlayerCubitState {}
