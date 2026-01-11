import 'package:hive_flutter/hive_flutter.dart';
import 'package:radio_online/models/radio_station.dart';
import 'package:radio_online/utility/app_languages.dart';
import 'package:shared_preferences/shared_preferences.dart';

class HiveUtility {
  static late final Box<int> _favorites;
  static late final Box<RadioStation> _radios;
  static late final SharedPreferences _sharedPreferences;
  static late final Box<String> _settingsBox;

  static Future<void> initializeHive() async {
    await Hive.initFlutter();
    Hive.registerAdapter(RadioStationAdapter());

    _sharedPreferences = await SharedPreferences.getInstance();
    _radios = await Hive.openBox('radios');
    _favorites = await Hive.openBox('favorites');
    _settingsBox = await Hive.openBox('settings');
  }

  ///Favorites Utility
  static void addToFavorite(RadioStation station) {
    _favorites.put(station.radioStationId, 0);
    if (!_radios.containsKey(station.radioStationId)) {
      _radios.put(station.radioStationId, station);
    }
  }

  static void deleteFromFavorite(int id) => _favorites.delete(id);

  static List<RadioStation> get favoriteList => _favorites.keys
      .map((key) => _radios.get(key))
      .toList()
      .cast<RadioStation>();

  static bool isFavoriteRadio(int id) => _favorites.containsKey(id);

  static void addFavoriteListener(void Function() callback) {
    _favorites.listenable().addListener(callback);
  }

  ///Cache Utility

  ///Theme Mode Cache => 0:light 1:dark
  static int get themeMode => _sharedPreferences.getInt('themeMode') ?? 0;

  static void setThemeMode(int mode) =>
      _sharedPreferences.setInt('themeMode', mode);

  ///Last Played Radio
  static RadioStation? get lastPlayed =>
      _radios.get(_sharedPreferences.getInt('lastPlayed'));

  static void setLastPlayed(RadioStation station) {
    if (!_radios.containsKey(station.radioStationId)) {
      _radios.put(station.radioStationId, station);
    }
    _sharedPreferences.setInt('lastPlayed', station.radioStationId);
  }

  static String get language =>
      _settingsBox.get('language') ?? defaultLanguageCode;

  static void setLanguage(String languageCode) =>
      _settingsBox.put('language', languageCode);
}
