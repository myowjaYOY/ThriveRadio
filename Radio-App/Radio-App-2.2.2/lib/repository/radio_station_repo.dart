import 'dart:convert';
import 'dart:io';
import 'package:radio_online/data/radio_station_provider.dart';
import 'package:radio_online/data/azuracast_provider.dart';
import 'package:radio_online/models/category_model.dart';
import 'package:radio_online/models/city_model.dart';
import 'package:radio_online/models/radio_station.dart';
import 'package:radio_online/models/slider_model.dart';
import 'package:radio_online/utility/app_info.dart';
import 'package:radio_online/utility/error_message_labels.dart';

class RadioStationRepo {
  factory RadioStationRepo() => _radioStationRepo;

  RadioStationRepo._internal();

  static final RadioStationRepo _radioStationRepo =
      RadioStationRepo._internal();

  final RadioStationProvider _radioStationProvider = RadioStationProvider();
  final AzuraCastProvider _azuraCastProvider = AzuraCastProvider();

  Future<bool> getAppSettings() async {
    try {
      final jsonString = jsonDecode(
        await _radioStationProvider.fetchAppSettings('app_setting'),
      );
      final jsonMap = jsonString as Map<String, dynamic>;

      if (jsonMap['error'] as bool == false) {
        AppInfo().initializeFields(jsonMap['data'] as Map<String, dynamic>);
        return true;
      } else {
        return false;
      }
    } on SocketException {
      throw Exception(noInternet);
    } on Exception {
      return false;
    }
  }

  /// Get sliders - returns empty for AzuraCast (no slider support)
  Future<List<SliderModel>> getSliders() async {
    // AzuraCast doesn't have sliders, return empty list
    return <SliderModel>[];
  }

  Future<List<CityModel>> getCities({
    required int limit,
    int? offset,
    String searchQuery = '',
  }) async {
    try {
      final jsonString = jsonDecode(
        await _radioStationProvider.fetchCities(
          limit: limit,
          offset: offset,
          searchQuery: searchQuery,
        ),
      );
      final jsonMap = jsonString as Map<String, dynamic>;

      if (jsonMap['error'] as bool == false) {
        return (jsonMap['data'] as List)
            .cast<Map<String, dynamic>>()
            .map(CityModel.fromJson)
            .toList();
      } else {
        throw Exception(jsonMap['message']);
      }
    } on FormatException {
      throw Exception('Invalid Data');
    } on SocketException {
      throw Exception(noInternet);
    }
  }

  Future<RadioStation?> getRadioStationById(int radioId) async {
    try {
      return await _radioStationProvider
          .fetchRadioStations(limit: 1, radioStationId: '$radioId')
          .then(jsonDecode)
          .then((jsonString) => jsonString as Map<String, dynamic>)
          .then((jsonMap) {
        if (jsonMap['error'] as bool == false) {
          final stationMap =
              (jsonMap['data'] as List).cast<Map<String, dynamic>>();
          return (stationMap.isEmpty)
              ? null
              : RadioStation.fromJson(stationMap[0]);
        } else {
          throw Exception(jsonMap['message']);
        }
      });
    } on Exception {
      throw Exception('Invalid Data');
    }
  }

  Future<List<RadioStation>> getRadioStationsByCity(int cityId) async {
    return getRadioStations(limit: 10, cityId: '$cityId');
  }

  Future<List<RadioStation>> getRadioStationsByCategory(int categoryId) async {
    return getRadioStations(
      limit: 10,
      categoryId: '$categoryId',
    );
  }

  Future<List<RadioStation>> getRadioStationsByQuery(String query) async {
    return getRadioStations(limit: 10, searchQuery: query);
  }

  /// Fetch radio stations from AzuraCast
  Future<List<RadioStation>> getRadioStations({
    required int limit,
    String offset = '0',
    String cityId = '',
    String categoryId = '',
    String searchQuery = '',
  }) async {
    try {
      // Fetch from AzuraCast nowplaying endpoint (includes station + now playing info)
      final jsonString = await _azuraCastProvider.fetchNowPlaying();
      final jsonList = jsonDecode(jsonString) as List;
      
      List<RadioStation> radioStationList = jsonList
          .cast<Map<String, dynamic>>()
          .map((json) => RadioStation.fromAzuraCast(json))
          .toList();
      
      // Apply search filter if provided
      if (searchQuery.isNotEmpty) {
        radioStationList = radioStationList
            .where((station) => station.radioStationName
                .toLowerCase()
                .contains(searchQuery.toLowerCase()))
            .toList();
      }
      
      // Apply limit
      if (radioStationList.length > limit) {
        radioStationList = radioStationList.take(limit).toList();
      }
      
      return radioStationList;
    } on SocketException {
      throw Exception(noInternet);
    } on FormatException {
      throw Exception('Invalid Data');
    }
  }

  /// Get categories - returns empty for AzuraCast (no category support)
  Future<List<CategoryModel>> getCategories({
    required int limit,
    int? offset,
    String searchQuery = '',
  }) async {
    // AzuraCast doesn't have categories, return empty list
    return <CategoryModel>[];
  }

  Future<bool> registerToken(String token) async =>
      _radioStationProvider.registerToken(token);

  Future<bool> reportStation(String id, String message) async =>
      _radioStationProvider.reportStation(id, message);

  Future<String> getLegalInfo(String legalInfo) async {
    try {
      return _radioStationProvider
          .fetchAppSettings(legalInfo)
          .then(jsonDecode)
          .then((jsonString) => jsonString as Map<String, dynamic>)
          .then((jsonMap) {
        if (jsonMap['error'] as bool == false) {
          return jsonMap['data'] as String;
        } else {
          throw Exception(jsonMap['message']);
        }
      });
    } on SocketException {
      throw Exception(noInternet);
    } on Exception {
      throw Exception('Invalid Data');
    }
  }
}
