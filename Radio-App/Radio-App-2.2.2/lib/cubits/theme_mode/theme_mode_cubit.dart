import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:radio_online/utility/hive_utility.dart';

enum Modes { light, dark }

class ThemeModeCubit extends Cubit<Modes> {
  ThemeModeCubit(int mode) : super(Modes.values[mode]);

  void changeTheme() {
    if (state == Modes.light) {
      HiveUtility.setThemeMode(1);
      emit(Modes.dark);
    } else {
      HiveUtility.setThemeMode(0);
      emit(Modes.light);
    }
  }
}
