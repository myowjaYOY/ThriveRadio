/// Analytics service for THRIVE Radio
/// 
/// Tracks user events: logins, logouts, signups, and station listens.
/// Data is stored in the `thrive_radio` schema in Supabase.
library;

import 'package:flutter/foundation.dart';
import 'package:radio_online/auth/config/supabase_config.dart';
import 'package:radio_online/auth/services/device_info_service.dart';

/// Event types for auth tracking
enum AuthEventType {
  login,
  logout,
  signup,
}

class AnalyticsService {
  AnalyticsService._();
  
  static final AnalyticsService _instance = AnalyticsService._();
  static AnalyticsService get instance => _instance;

  final DeviceInfoService _deviceInfo = DeviceInfoService.instance;

  /// Current active listen session ID (null if not listening)
  String? _currentListenSessionId;

  // ============================================
  // AUTH EVENT TRACKING
  // ============================================

  /// Track an authentication event (login, logout, signup)
  Future<void> trackAuthEvent(AuthEventType eventType) async {
    final userId = currentUserId;
    if (userId == null) return;

    try {
      final deviceInfo = await _deviceInfo.getDeviceInfo();
      
      await supabase.schema('thrive_radio').from('auth_events').insert({
        'user_id': userId,
        'event_type': eventType.name,
        'device_info': deviceInfo,
      });
    } catch (e) {
      // Silently fail - analytics should not break the app
      debugPrint('Analytics error (auth event): $e');
    }
  }

  /// Convenience methods for auth events
  Future<void> trackLogin() => trackAuthEvent(AuthEventType.login);
  Future<void> trackLogout() => trackAuthEvent(AuthEventType.logout);
  Future<void> trackSignup() => trackAuthEvent(AuthEventType.signup);

  // ============================================
  // STATION LISTEN TRACKING
  // ============================================

  /// Track when a user starts listening to a station
  /// Returns the session ID for later ending the session
  Future<String?> trackStationStart({
    required int stationId,
    required String stationName,
  }) async {
    debugPrint('DEBUG: trackStationStart() called - station: $stationName (id: $stationId)');
    final userId = currentUserId;
    if (userId == null) {
      debugPrint('DEBUG: trackStationStart() - no user logged in, returning null');
      return null;
    }

    // End any existing session first
    debugPrint('DEBUG: trackStationStart() - ending existing session first');
    await endCurrentListenSession();

    try {
      final deviceInfo = await _deviceInfo.getDeviceInfo();
      
      final response = await supabase
          .schema('thrive_radio')
          .from('station_listens')
          .insert({
            'user_id': userId,
            'station_id': stationId,
            'station_name': stationName,
            'device_info': deviceInfo,
          })
          .select('id')
          .single();

      _currentListenSessionId = response['id'] as String?;
      return _currentListenSessionId;
    } catch (e) {
      debugPrint('Analytics error (station start): $e');
      return null;
    }
  }

  /// Track when a user stops listening (station change, pause, or app close)
  Future<void> trackStationEnd({String? sessionId}) async {
    final id = sessionId ?? _currentListenSessionId;
    if (id == null) return;

    try {
      // Get the session to calculate duration
      final session = await supabase
          .schema('thrive_radio')
          .from('station_listens')
          .select('started_at')
          .eq('id', id)
          .maybeSingle();

      if (session == null) return;

      final startedAt = DateTime.parse(session['started_at'] as String);
      final now = DateTime.now().toUtc();
      final durationSeconds = now.difference(startedAt).inSeconds;

      await supabase
          .schema('thrive_radio')
          .from('station_listens')
          .update({
            'ended_at': now.toIso8601String(),
            'duration_seconds': durationSeconds,
          })
          .eq('id', id);

      if (id == _currentListenSessionId) {
        _currentListenSessionId = null;
      }
    } catch (e) {
      debugPrint('Analytics error (station end): $e');
    }
  }

  /// End the current listen session if one exists
  Future<void> endCurrentListenSession() async {
    debugPrint('DEBUG: endCurrentListenSession() called - currentSessionId: $_currentListenSessionId');
    if (_currentListenSessionId != null) {
      debugPrint('DEBUG: endCurrentListenSession() - calling trackStationEnd');
      await trackStationEnd();
    } else {
      debugPrint('DEBUG: endCurrentListenSession() - no active session, skipping');
    }
  }

  /// Check if there's an active listen session
  bool get hasActiveListenSession => _currentListenSessionId != null;

  /// Get the current listen session ID
  String? get currentListenSessionId => _currentListenSessionId;
}
