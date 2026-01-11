import 'package:package_info_plus/package_info_plus.dart';
import 'package:radio_online/utility/app_info.dart';

class AppUtilities {
  static Future<bool> shouldForceAppUpdate() async {
    if (AppInfo().shouldForceAppUpdate) {
      final info = await PackageInfo.fromPlatform();
      final currAppVersion = '${info.version}+${info.buildNumber}';

      final updateBasedOnVersion = _shouldUpdateBasedOnVersion(
        currAppVersion.split('+').first,
        AppInfo().appVersion.split('+').first,
      );

      if (AppInfo().appVersion.split('+').length == 1 ||
          currAppVersion.split('+').length == 1) {
        return updateBasedOnVersion;
      }

      final updateBasedOnBuildNumber = _shouldUpdateBasedOnBuildNumber(
        currAppVersion.split('+').last,
        AppInfo().appVersion.split('+').last,
      );

      return updateBasedOnVersion || updateBasedOnBuildNumber;
    }
    return false;
  }

  static bool _shouldUpdateBasedOnVersion(
    String currentVersion,
    String updatedVersion,
  ) {
    final currentVersionList =
        currentVersion.split('.').map(int.parse).toList();
    final updatedVersionList =
        updatedVersion.split('.').map(int.parse).toList();

    if (updatedVersionList[0] > currentVersionList[0]) {
      return true;
    }
    if (updatedVersionList[1] > currentVersionList[1]) {
      return true;
    }
    if (updatedVersionList[2] > currentVersionList[2]) {
      return true;
    }

    return false;
  }

  static bool _shouldUpdateBasedOnBuildNumber(
    String currentBuildNumber,
    String updatedBuildNumber,
  ) {
    return int.parse(updatedBuildNumber) > int.parse(currentBuildNumber);
  }
}
