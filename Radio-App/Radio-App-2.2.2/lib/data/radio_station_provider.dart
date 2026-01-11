import 'package:http/http.dart' as http;
import 'package:radio_online/utility/constants.dart';

class RadioStationProvider {
  final String _apiUrl = '$baseUrl/api/';
  
  // Short timeout so app doesn't hang when backend is unavailable
  static const Duration _timeout = Duration(seconds: 3);

  Future<String> fetchSliders() async {
    return http.get(Uri.parse('${_apiUrl}get-slider'))
        .timeout(_timeout)
        .then((response) {
      if (response.statusCode == 200) {
        return response.body;
      } else {
        throw Exception('Internal Server Error');
      }
    });
  }

  Future<String> fetchRadioStations({
    required int limit,
    String offset = '',
    String cityId = '',
    String categoryId = '',
    String radioStationId = '',
    String searchQuery = '',
  }) async {
    return http
        .get(
      Uri.parse('${_apiUrl}get-radio-station?'
          'limit=$limit'
          '&offset=$offset'
          '&city_id=$cityId'
          '&category_id=$categoryId'
          '&radio_station_id=$radioStationId'
          '&search=$searchQuery'),
    )
        .timeout(_timeout)
        .then((http.Response response) {
      if (response.statusCode == 200) {
        return response.body;
      } else {
        throw Exception('Internal Server Error');
      }
    });
  }

  Future<String> fetchCategories({
    required int limit,
    int? offset,
    String searchQuery = '',
  }) async {
    return http
        .get(
      Uri.parse('${_apiUrl}get-category?'
          'limit=$limit'
          '&offset=$offset'
          '&search=$searchQuery'),
    )
        .timeout(_timeout)
        .then((response) {
      if (response.statusCode == 200) {
        return response.body;
      } else {
        throw Exception('Internal Server Error');
      }
    });
  }

  Future<String> fetchCities({
    required int limit,
    int? offset,
    String searchQuery = '',
  }) async {
    return http
        .get(
      Uri.parse(
        '${_apiUrl}get-city?'
        'limit=$limit'
        '&offset=$offset'
        '&search=$searchQuery',
      ),
    )
        .then((response) {
      if (response.statusCode == 200) {
        return response.body;
      } else {
        throw Exception('Internal Server Error');
      }
    });
  }

  Future<String> fetchAppSettings(String setting) async {
    return http
        .get(
      Uri.parse('${_apiUrl}get-settings?type=$setting'),
    )
        .timeout(_timeout)
        .then((response) {
      if (response.statusCode == 200) {
        return response.body;
      } else {
        throw Exception('Internal Server Error');
      }
    });
  }

  Future<bool> registerToken(String token) async {
    return http
        .post(
          Uri.parse('${_apiUrl}register-token'),
          body: {'token': token},
        )
        .then((response) => response.statusCode == 200)
        .onError((error, stackTrace) => false);
  }

  Future<bool> reportStation(String radioId, String message) async {
    return http.post(
      Uri.parse('${_apiUrl}report-radio-station'),
      body: {
        'radio_station_id': radioId,
        'message': message,
        'date': getDate(),
      },
    ).then((response) => response.statusCode == 200);
  }

  String getDate() {
    final now = DateTime.now();
    return DateTime(now.year, now.month, now.day).toString();
  }
}
