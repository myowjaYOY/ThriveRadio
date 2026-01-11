import 'dart:async';
import 'dart:math';

import 'package:awesome_notifications/awesome_notifications.dart';
import 'package:firebase_messaging/firebase_messaging.dart';

class NotificationUtility {
  ///Firebase notification utility
  static late final FirebaseMessaging _firebaseMessaging;
  static late final AwesomeNotifications _awesomeNotifications;

  static void initializeFirebaseMessaging() {
    _firebaseMessaging = FirebaseMessaging.instance;
    FirebaseMessaging.onMessage.listen(_createNotificationForForeground);
    _awesomeNotifications = AwesomeNotifications();
    _awesomeNotifications.initialize(null, [
      NotificationChannel(
        channelKey: 'firebase_notifications',
        channelName: 'foreground_notifications',
        channelDescription: 'A channel for foreground firebase notifications',
        importance: NotificationImportance.High,
      ),
    ]);
  }

  static void _createNotificationForForeground(RemoteMessage message) {
    final image = message.data['image'].toString().isNotEmpty
        ? message.data['image'] as String
        : null;

    _awesomeNotifications.createNotification(
      content: NotificationContent(
        id: Random().nextInt(5000),
        channelKey: 'firebase_notifications',
        title: message.notification?.title,
        body: message.notification?.body,
        bigPicture: image,
        notificationLayout: image != null
            ? NotificationLayout.BigPicture
            : NotificationLayout.Default,
      ),
    );
  }

  static Future<bool> requestPermission() async {
    final notificationSettings =
        await _firebaseMessaging.getNotificationSettings();
    if (notificationSettings.authorizationStatus ==
            AuthorizationStatus.authorized ||
        notificationSettings.authorizationStatus ==
            AuthorizationStatus.provisional) {
      return true;
    } else if (notificationSettings.authorizationStatus ==
            AuthorizationStatus.notDetermined ||
        notificationSettings.authorizationStatus ==
            AuthorizationStatus.denied) {
      final settings = await _firebaseMessaging.requestPermission();
      if (settings.authorizationStatus == AuthorizationStatus.denied ||
          settings.authorizationStatus == AuthorizationStatus.notDetermined) {
        return false;
      } else {
        return true;
      }
    } else {
      return false;
    }
  }
}
