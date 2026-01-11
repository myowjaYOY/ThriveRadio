import 'package:http/http.dart' as http;
import 'package:radio_online/utility/constants.dart';

/// Provider for fetching data directly from AzuraCast API
class AzuraCastProvider {
  static const Duration _timeout = Duration(seconds: 10);

  /// Fetch all stations from AzuraCast
  Future<String> fetchStations() async {
    final response = await http
        .get(Uri.parse('$azuraCastApiUrl/stations'))
        .timeout(_timeout);
    
    if (response.statusCode == 200) {
      return response.body;
    } else {
      throw Exception('Failed to load stations: ${response.statusCode}');
    }
  }

  /// Fetch now playing data for all stations
  Future<String> fetchNowPlaying() async {
    final response = await http
        .get(Uri.parse('$azuraCastApiUrl/nowplaying'))
        .timeout(_timeout);
    
    if (response.statusCode == 200) {
      return response.body;
    } else {
      throw Exception('Failed to load now playing: ${response.statusCode}');
    }
  }

  /// Fetch now playing data for a specific station
  Future<String> fetchNowPlayingByStation(int stationId) async {
    final response = await http
        .get(Uri.parse('$azuraCastApiUrl/nowplaying/$stationId'))
        .timeout(_timeout);
    
    if (response.statusCode == 200) {
      return response.body;
    } else {
      throw Exception('Failed to load now playing: ${response.statusCode}');
    }
  }

  /// Fetch specific station details
  Future<String> fetchStation(int stationId) async {
    final response = await http
        .get(Uri.parse('$azuraCastApiUrl/station/$stationId'))
        .timeout(_timeout);
    
    if (response.statusCode == 200) {
      return response.body;
    } else {
      throw Exception('Failed to load station: ${response.statusCode}');
    }
  }
}

