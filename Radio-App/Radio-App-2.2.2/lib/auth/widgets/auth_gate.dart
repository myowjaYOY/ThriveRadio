/// Auth gate widget - Wraps the app to enforce authentication
library;

import 'package:flutter/material.dart';
import 'package:supabase_flutter/supabase_flutter.dart';

import 'package:radio_online/auth/config/supabase_config.dart';
import 'package:radio_online/auth/screens/welcome_screen.dart';

/// Widget that gates access to the app based on authentication state.
/// Shows [WelcomeScreen] when not authenticated, otherwise shows [child].
class AuthGate extends StatefulWidget {
  const AuthGate({
    required this.child,
    super.key,
  });

  /// The widget to show when authenticated
  final Widget child;

  @override
  State<AuthGate> createState() => _AuthGateState();
}

class _AuthGateState extends State<AuthGate> {
  @override
  Widget build(BuildContext context) {
    return StreamBuilder<AuthState>(
      stream: supabase.auth.onAuthStateChange,
      builder: (context, snapshot) {
        // Show loading while checking initial auth state
        if (snapshot.connectionState == ConnectionState.waiting) {
          return const Scaffold(
            body: Center(
              child: CircularProgressIndicator(),
            ),
          );
        }

        // Check if user is authenticated
        final session = supabase.auth.currentSession;
        
        if (session != null) {
          // User is authenticated, show the app
          return widget.child;
        } else {
          // User is not authenticated, show welcome screen
          return const WelcomeScreen();
        }
      },
    );
  }
}
