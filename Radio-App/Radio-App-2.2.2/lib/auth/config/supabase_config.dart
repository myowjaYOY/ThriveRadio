/// Supabase configuration for THRIVE Radio
/// 
/// This file contains the Supabase project credentials.
/// The app uses the `thrive_radio` schema for all data.
library;

import 'package:supabase_flutter/supabase_flutter.dart';

/// Supabase project URL
const String supabaseUrl = 'https://mxktlbhiknpdauzoitnm.supabase.co';

/// Supabase anonymous key (safe to include in client code)
const String supabaseAnonKey = 
    'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im14a3RsYmhpa25wZGF1em9pdG5tIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDU0NDgzODcsImV4cCI6MjA2MTAyNDM4N30.xtu2QK-pj8_Dx5k1wseQsX7-iyt-YYc2cJay06sAFi8';

/// Initialize Supabase
/// 
/// Call this in main() before runApp()
Future<void> initializeSupabase() async {
  await Supabase.initialize(
    url: supabaseUrl,
    anonKey: supabaseAnonKey,
  );
}

/// Get the Supabase client instance
SupabaseClient get supabase => Supabase.instance.client;

/// Check if user is currently authenticated
bool get isAuthenticated => supabase.auth.currentUser != null;

/// Get current user ID (null if not authenticated)
String? get currentUserId => supabase.auth.currentUser?.id;
