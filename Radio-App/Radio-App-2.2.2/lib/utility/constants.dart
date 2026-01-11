import 'package:flutter/material.dart';
import 'package:radio_online/utility/app_info.dart';

/// Add your admin panel url here (legacy - not used with AzuraCast)
// NOTE: make sure to not add '/' at the end of url
// NOTE: make sure to check if admin panel is http or https
const baseUrl = 'PLACE_YOUR_HOST_URL_HERE';

/// AzuraCast API Configuration
const azuraCastUrl = 'https://azuracast-radio-u62352.vm.elestio.app';
const azuraCastApiUrl = '$azuraCastUrl/api';

/// Check if backend is configured (now checks AzuraCast)
bool get isBackendConfigured => azuraCastUrl.isNotEmpty && azuraCastUrl != 'PLACE_YOUR_AZURACAST_URL_HERE';

/// Fallbacks
const kPrimaryColor = Color(0xffb0506a);
const kPrimaryBackgroundColor = Color(0xffe7e4e4);
const kFallbackImage = 'assets/images/placeholder.png';

final shareAppText =
    'Hey, I am listening to Radio on ${AppInfo().appName}\n ${AppInfo().appLink}';

String shareNowPlaying(String stationName) {
  return 'Hey, I am listening to $stationName On ${AppInfo().appName}'
      '\n${AppInfo().appLink}';
}
