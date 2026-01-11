import 'package:flutter_bloc/flutter_bloc.dart';

enum AppSettingsStatus { initial, loading, success, failure }

class AppSettingsState {
  AppSettingsState({
    this.status = AppSettingsStatus.initial,
    this.error,
  });

  final AppSettingsStatus status;
  final String? error;

  AppSettingsState copyWith({
    AppSettingsStatus? status,
    String? error,
  }) {
    return AppSettingsState(
      status: status ?? this.status,
      error: error ?? this.error,
    );
  }
}

class AppSettingsCubit extends Cubit<AppSettingsState> {
  AppSettingsCubit() : super(AppSettingsState());

  /// Using AzuraCast - no need to fetch settings from PHP backend
  Future<void> fetchAppSettings() async {
    // Immediately emit success - settings come from app defaults
    emit(state.copyWith(status: AppSettingsStatus.success));
  }
}
