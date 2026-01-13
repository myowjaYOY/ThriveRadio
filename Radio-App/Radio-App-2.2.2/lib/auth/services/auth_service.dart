/// Authentication service for THRIVE Radio
/// 
/// Handles all authentication operations using Supabase Auth.
/// User profiles are stored in `thrive_radio.profiles`.
library;

import 'dart:async';

import 'package:flutter/foundation.dart';
import 'package:supabase_flutter/supabase_flutter.dart';

import 'package:radio_online/auth/config/supabase_config.dart';
import 'package:radio_online/auth/models/user_profile.dart';
import 'package:radio_online/auth/services/analytics_service.dart';

/// Result of an authentication operation
class AuthResult {
  AuthResult({
    required this.success,
    this.errorMessage,
    this.user,
    this.profile,
    this.requiresEmailConfirmation = false,
  });

  final bool success;
  final String? errorMessage;
  final User? user;
  final UserProfile? profile;
  final bool requiresEmailConfirmation;

  factory AuthResult.success({
    User? user, 
    UserProfile? profile,
    bool requiresEmailConfirmation = false,
  }) {
    return AuthResult(
      success: true, 
      user: user, 
      profile: profile,
      requiresEmailConfirmation: requiresEmailConfirmation,
    );
  }

  factory AuthResult.failure(String message) {
    return AuthResult(success: false, errorMessage: message);
  }
}

/// Authentication service singleton
class AuthService {
  AuthService._();
  
  static final AuthService _instance = AuthService._();
  static AuthService get instance => _instance;

  final AnalyticsService _analytics = AnalyticsService.instance;

  /// Stream of auth state changes
  Stream<AuthState> get authStateChanges => supabase.auth.onAuthStateChange;

  /// Current authenticated user (null if not logged in)
  User? get currentUser => supabase.auth.currentUser;

  /// Current user's profile (cached)
  UserProfile? _cachedProfile;
  UserProfile? get cachedProfile => _cachedProfile;

  /// Check if user is authenticated
  bool get isAuthenticated => currentUser != null;

  // ============================================
  // SIGN UP
  // ============================================

  /// Register a new user
  Future<AuthResult> signUp({
    required String email,
    required String password,
    required String firstName,
    required String lastName,
    String? phone,
  }) async {
    try {
      // Sign up with Supabase Auth
      final response = await supabase.auth.signUp(
        email: email,
        password: password,
        data: {
          'first_name': firstName,
          'last_name': lastName,
          'phone': phone,
        },
      );

      if (response.user == null) {
        return AuthResult.failure('Sign up failed. Please try again.');
      }

      // Check if email confirmation is required
      // If session is null, email confirmation is likely required
      final requiresConfirmation = response.session == null;

      // Create profile in thrive_radio schema (if user confirmed or auto-confirmed)
      UserProfile? profile;
      if (!requiresConfirmation) {
        profile = await _ensureProfileExists(
          firstName: firstName,
          lastName: lastName,
          phone: phone,
        );
        // Track signup event
        await _analytics.trackSignup();
      }

      return AuthResult.success(
        user: response.user, 
        profile: profile,
        requiresEmailConfirmation: requiresConfirmation,
      );
    } on AuthException catch (e) {
      return AuthResult.failure(_mapAuthError(e));
    } catch (e) {
      return AuthResult.failure('An unexpected error occurred: $e');
    }
  }

  // ============================================
  // SIGN IN
  // ============================================

  /// Sign in with email and password
  Future<AuthResult> signIn({
    required String email,
    required String password,
  }) async {
    try {
      final response = await supabase.auth.signInWithPassword(
        email: email,
        password: password,
      );

      if (response.user == null) {
        return AuthResult.failure('Sign in failed. Please try again.');
      }

      // Ensure profile exists (for existing users from other apps)
      final profile = await _ensureProfileExists();

      // Track login event
      await _analytics.trackLogin();

      return AuthResult.success(user: response.user, profile: profile);
    } on AuthException catch (e) {
      return AuthResult.failure(_mapAuthError(e));
    } catch (e) {
      return AuthResult.failure('An unexpected error occurred: $e');
    }
  }

  // ============================================
  // SIGN OUT
  // ============================================

  /// Sign out the current user
  Future<AuthResult> signOut() async {
    try {
      // End any active listen session
      await _analytics.endCurrentListenSession();
      
      // Track logout event before signing out
      await _analytics.trackLogout();

      await supabase.auth.signOut();
      _cachedProfile = null;

      return AuthResult.success();
    } catch (e) {
      return AuthResult.failure('Sign out failed: $e');
    }
  }

  // ============================================
  // PASSWORD RESET
  // ============================================

  /// Send password reset email
  /// Returns null on success, or an error message on failure
  Future<String?> resetPassword(String email) async {
    try {
      await supabase.auth.resetPasswordForEmail(email);
      return null; // Success
    } on AuthException catch (e) {
      return _mapAuthError(e);
    } catch (e) {
      return 'Failed to send reset email: $e';
    }
  }

  // ============================================
  // PROFILE MANAGEMENT
  // ============================================

  /// Get the current user's profile
  Future<UserProfile?> getProfile() async {
    if (currentUser == null) return null;

    try {
      final response = await supabase
          .schema('thrive_radio')
          .from('profiles')
          .select()
          .eq('id', currentUser!.id)
          .maybeSingle();

      if (response != null) {
        _cachedProfile = UserProfile.fromJson(response);
        return _cachedProfile;
      }
      return null;
    } catch (e) {
      debugPrint('Error fetching profile: $e');
      return null;
    }
  }

  /// Check if current user has a THRIVE Radio profile
  Future<bool> hasProfile() async {
    if (currentUser == null) return false;

    try {
      final response = await supabase
          .schema('thrive_radio')
          .from('profiles')
          .select('id')
          .eq('id', currentUser!.id)
          .maybeSingle();
      return response != null;
    } catch (e) {
      debugPrint('Error checking profile: $e');
      return false;
    }
  }

  /// Ensure profile exists, creating if necessary
  Future<UserProfile?> _ensureProfileExists({
    String? firstName,
    String? lastName,
    String? phone,
  }) async {
    if (currentUser == null) return null;

    try {
      // Try to get user metadata if names not provided
      final metadata = currentUser!.userMetadata;
      final fName = firstName ?? metadata?['first_name'] as String? ?? '';
      final lName = lastName ?? metadata?['last_name'] as String? ?? '';
      final phn = phone ?? metadata?['phone'] as String?;

      // Check if profile already exists
      final existing = await supabase
          .schema('thrive_radio')
          .from('profiles')
          .select()
          .eq('id', currentUser!.id)
          .maybeSingle();

      if (existing != null) {
        _cachedProfile = UserProfile.fromJson(existing);
        return _cachedProfile;
      }

      // Create new profile
      final response = await supabase
          .schema('thrive_radio')
          .from('profiles')
          .insert({
            'id': currentUser!.id,
            'first_name': fName,
            'last_name': lName,
            'email': currentUser!.email ?? '',
            'phone': phn,
          })
          .select()
          .single();

      _cachedProfile = UserProfile.fromJson(response);
      return _cachedProfile;
    } catch (e) {
      debugPrint('Error ensuring profile: $e');
      // Fallback: try to get existing profile
      return getProfile();
    }
  }

  /// Update user profile
  Future<AuthResult> updateProfile({
    String? firstName,
    String? lastName,
    String? phone,
  }) async {
    if (currentUser == null) {
      return AuthResult.failure('Not authenticated');
    }

    try {
      final updates = <String, dynamic>{};
      if (firstName != null) updates['first_name'] = firstName;
      if (lastName != null) updates['last_name'] = lastName;
      if (phone != null) updates['phone'] = phone;

      if (updates.isEmpty) {
        return AuthResult.failure('No updates provided');
      }

      await supabase
          .schema('thrive_radio')
          .from('profiles')
          .update(updates)
          .eq('id', currentUser!.id);

      // Refresh cached profile
      final profile = await getProfile();
      return AuthResult.success(profile: profile);
    } catch (e) {
      return AuthResult.failure('Failed to update profile: $e');
    }
  }

  // ============================================
  // HELPERS
  // ============================================

  /// Map Supabase auth errors to user-friendly messages
  String _mapAuthError(AuthException e) {
    final message = e.message.toLowerCase();
    
    if (message.contains('invalid login credentials')) {
      return 'Invalid email or password';
    }
    if (message.contains('email not confirmed')) {
      return 'Please verify your email address';
    }
    if (message.contains('user already registered')) {
      return 'An account with this email already exists';
    }
    if (message.contains('password')) {
      return 'Password must be at least 6 characters';
    }
    if (message.contains('email')) {
      return 'Please enter a valid email address';
    }
    if (message.contains('rate limit')) {
      return 'Too many attempts. Please try again later';
    }
    
    return e.message;
  }
}
