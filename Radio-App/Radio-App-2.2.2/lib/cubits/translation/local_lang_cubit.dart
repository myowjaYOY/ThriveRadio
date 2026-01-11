import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:radio_online/utility/hive_utility.dart';

class LocaleCubit extends Cubit<Locale> {
  LocaleCubit() : super(Locale(HiveUtility.language));
  void setLocale(Locale locale) => emit(locale);
}
