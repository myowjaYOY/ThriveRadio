import 'dart:io';
import 'dart:ui';

import 'package:radio_online/utility/constants.dart';

class AppInfo {
  factory AppInfo() => _appInfo;

  AppInfo._internal();

  void initializeFields(Map<String, dynamic> json) {
    _lightPrimaryColor = extractColor(json['primary_color'] as String);
    _lightBackgroundColor = extractColor(json['background_color'] as String);

    ///Uncomment when dark mode fields are added to admin panel
    // _darkPrimaryColor = extractColor(jsonData['dark_primary_color'] as String);
    // _darkBackgroundColor =
    //     extractColor(jsonData['dark_background_color'] as String);
    _cityMode = json['city_mode'] == '1';
    _appName = json['app_name'] as String;
    _bannerAdIdAndroid = json['banner_ad_id_android'] ?? '';
    _bannerAdIdIos = json['banner_ad_id_ios'] ?? '';
    _interstitialAdIdAndroid = json['interstitial_ad_id_android'] ?? '';
    _interstitialAdIdIos = json['interstitial_ad_id_ios'] ?? '';
    _androidAppLink = json['app_link'] ?? '';
    _iosAppLink = json['ios_app_link'] ?? '';
    _androidAppVersion = json['app_version'] ?? '';
    _iosAppVersion = json['ios_app_version'] ?? '';
    _forceAppUpdate = json['force_app_update'] == '1';
    _appMaintenance = json['app_maintenance'] == '1';
  }

  static final AppInfo _appInfo = AppInfo._internal();

  // Default values for when API is unavailable
  Color _lightPrimaryColor = kPrimaryColor;
  Color _lightBackgroundColor = kPrimaryBackgroundColor;
  Color _darkPrimaryColor = kPrimaryColor;
  Color _darkBackgroundColor = kPrimaryBackgroundColor;
  bool _cityMode = false;
  String _appName = 'THRIVE Radio';
  String _bannerAdIdAndroid = '';
  String _bannerAdIdIos = '';
  String _interstitialAdIdAndroid = '';
  String _interstitialAdIdIos = '';
  String _androidAppLink = '';
  String _iosAppLink = '';
  String _androidAppVersion = '1.0.0';
  String _iosAppVersion = '1.0.0';
  bool _forceAppUpdate = false;
  bool _appMaintenance = false;

  Color extractColor(String hex) {
    final hexString = "ff${hex.replaceAll("#", "")}";
    final hexValue = int.parse(hexString, radix: 16);
    return Color(hexValue);
  }

  Color get lightPrimaryColor => _lightPrimaryColor;

  Color get lightBackgroundColor => _lightBackgroundColor;

  bool get cityMode => _cityMode;

  String get appName => _appName;

  Color get darkPrimaryColor => _darkPrimaryColor;

  Color get darkBackgroundColor => _darkBackgroundColor;

  String get bannerAdId =>
      Platform.isAndroid ? _bannerAdIdAndroid : _bannerAdIdIos;

  String get interstitialAdId =>
      Platform.isAndroid ? _interstitialAdIdAndroid : _interstitialAdIdIos;

  String get appLink => Platform.isAndroid ? _androidAppLink : _iosAppLink;

  String get appVersion =>
      Platform.isAndroid ? _androidAppVersion : _iosAppVersion;

  bool get shouldForceAppUpdate => _forceAppUpdate;

  bool get isAppMaintenanceEnabled => _appMaintenance;
}
