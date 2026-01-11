# THRIVE Radio App - Project Brief

> **Template:** Radio Online (Flutter) from CodeCanyon  
> **Purchase URL:** https://codecanyon.net/item/radio-online/25624339  
> **Created:** December 2024

---

## Executive Summary

This project uses the **Radio Online** Flutter template to build a branded radio streaming app for THRIVE Radio. The app will connect to an existing AzuraCast server hosting 20 Barix encoder streams and require user authentication via Supabase before accessing content (paywall).

---

## Project Requirements

| Requirement | Solution |
|-------------|----------|
| Cross-platform (iOS + Android) | Flutter (Radio Online template) |
| Simple and fast to deploy | Pre-built template, config only |
| AzuraCast streaming server | Standard stream URLs compatible |
| Authentication/Paywall | Supabase Auth (Phase 3) |
| Consistent UI on both platforms | Flutter ensures this |
| CarPlay / Android Auto | Phase 4 (future consideration) |
| Custom branding | Phase 2 |

---

## Architecture Overview

```
+-------------------------------------------------------------+
|                    THRIVE Radio App                          |
|                   (Radio Online Template)                   |
+-------------------------------------------------------------+
|                                                             |
|  +---------------+  +-----------------+  +---------------+  |
|  |   Supabase    |  |  Radio Online   |  |  AzuraCast    |  |
|  |   Auth        |  |  PHP Backend    |  |  API          |  |
|  |               |  |                 |  |               |  |
|  | - User Login  |  | - Station Mgmt  |  | - Streams     |  |
|  | - Registration|  | - Categories    |  | - Now Playing |  |
|  | - Session Mgmt|  | - Push Notif    |  | - Metadata    |  |
|  +---------------+  +-----------------+  +---------------+  |
|         ^                  ^                    ^           |
|         |                  |                    |           |
|         +------------------+--------------------+           |
|                            |                                |
|                  +---------+---------+                      |
|                  |   Flutter App     |                      |
|                  |   (iOS/Android)   |                      |
|                  +-------------------+                      |
|                                                             |
+-------------------------------------------------------------+
```

---

## Technology Stack

| Component | Technology | Notes |
|-----------|------------|-------|
| **Mobile Framework** | Flutter | Cross-platform, single codebase |
| **Language** | Dart | Flutter's native language |
| **Template** | Radio Online | CodeCanyon template with PHP admin |
| **Streaming Server** | AzuraCast | Self-hosted, 20 Barix streams |
| **Authentication** | Supabase Auth | Email/password login |
| **Database** | Supabase (PostgreSQL) | User data, preferences |
| **Admin Panel** | PHP (included) | Manage stations, categories |

---

## AzuraCast Integration Details

### Your AzuraCast Server
```
Base URL: [YOUR_AZURACAST_URL]
Example: https://radio.yourdomain.com
```

### Available API Endpoints

| Endpoint | Purpose | Auth Required |
|----------|---------|:-------------:|
| `GET /api/stations` | List all stations | No |
| `GET /api/nowplaying` | Now playing for all stations | No |
| `GET /api/nowplaying/{id}` | Now playing for one station | No |
| `GET /api/station/{id}` | Station details | No |

### API Response Example (Now Playing)
```json
{
  "station": {
    "id": 1,
    "name": "THRIVE Main",
    "listen_url": "https://radio.yourdomain.com/listen/THRIVE/radio.mp3",
    "description": "24/7 Music Stream"
  },
  "now_playing": {
    "song": {
      "title": "Song Title",
      "artist": "Artist Name",
      "album": "Album Name",
      "art": "https://radio.yourdomain.com/api/station/1/art"
    },
    "elapsed": 45,
    "duration": 240
  },
  "listeners": {
    "current": 127
  }
}
```

### Stream URL Format
```
MP3:  https://radio.yourdomain.com/listen/{station}/radio.mp3
AAC:  https://radio.yourdomain.com/listen/{station}/radio.aac
OGG:  https://radio.yourdomain.com/listen/{station}/radio.ogg
```

### Your 20 Barix Streams
Each Barix encoder feeds into AzuraCast. For Phase 1, you will need to:
1. Get the stream URL for each station from AzuraCast dashboard
2. Enter each URL into Radio Online's admin panel
3. Add station name, description, and artwork

---

## Supabase Configuration

### Existing Supabase Project
```
URL: [YOUR_SUPABASE_PROJECT_URL]
Anon Key: [YOUR_SUPABASE_ANON_KEY]
```

### Authentication Requirements
- Email/Password registration and login
- Users must be authenticated to access radio streams
- Guest access NOT allowed (paywall)
- Remember login session across app restarts

### Supabase Auth Flow
```
App Launch
    |
    v
Check: supabase.auth.currentUser
    |
    v
+-------------------+-------------------+
|   User EXISTS     |   User NULL       |
|        |          |        |          |
|        v          |        v          |
|   Show Radio App  |   Show Login      |
|                   |        |          |
|                   |        v          |
|                   |   Login/Register  |
|                   |        |          |
|                   |        v          |
|                   |   On Success ->   |
|                   |   Show Radio App  |
+-------------------+-------------------+
```

### Flutter Supabase Integration Code
```dart
// pubspec.yaml
dependencies:
  supabase_flutter: ^2.0.0

// main.dart
import 'package:supabase_flutter/supabase_flutter.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  await Supabase.initialize(
    url: 'YOUR_SUPABASE_URL',
    anonKey: 'YOUR_SUPABASE_ANON_KEY',
  );
  
  runApp(MyApp());
}

// Check auth state
final user = Supabase.instance.client.auth.currentUser;
if (user != null) {
  // Authenticated - show radio app
} else {
  // Not authenticated - show login screen
}

// Sign up
await Supabase.instance.client.auth.signUp(
  email: email,
  password: password,
);

// Sign in
await Supabase.instance.client.auth.signInWithPassword(
  email: email,
  password: password,
);

// Sign out
await Supabase.instance.client.auth.signOut();
```

---

## Branding Requirements

### App Identity
| Element | Value |
|---------|-------|
| App Name | THRIVE Radio |
| Bundle ID (iOS) | com.THRIVE.radio |
| Package Name (Android) | com.THRIVE.radio |

### Colors (Update in Flutter theme)
```dart
// Define your brand colors
static const Color primary = Color(0xFF______);    // Main brand color
static const Color secondary = Color(0xFF______);  // Accent color
static const Color background = Color(0xFF______); // App background
static const Color text = Color(0xFF______);       // Primary text
```

### Assets Needed
| Asset | Size | Format |
|-------|------|--------|
| App Icon | 1024x1024 | PNG |
| Splash Screen | 1242x2688 | PNG |
| Logo (Light) | 512x512 | PNG (transparent) |
| Logo (Dark) | 512x512 | PNG (transparent) |
| Default Station Art | 500x500 | PNG/JPG |

---

## PHASE 1: Setup and Configuration

**Goal:** Get Radio Online template running with AzuraCast streams

**Duration:** 1-2 days

### Tasks
- [ ] Purchase Radio Online template from CodeCanyon
- [ ] Download and extract template files
- [ ] Set up Flutter development environment
- [ ] Set up PHP backend/admin panel on hosting
- [ ] Configure database for admin panel
- [ ] Run Flutter app in emulator/simulator
- [ ] Access admin panel via browser
- [ ] Add all 20 AzuraCast stations via admin panel:
  - Station name
  - Stream URL (from AzuraCast)
  - Station artwork
  - Category assignment
- [ ] Test playback of all stations in emulator
- [ ] Verify stream quality and stability

### Verification Checklist
- [ ] App launches without errors
- [ ] All 20 stations appear in station list
- [ ] Each station plays audio correctly
- [ ] Station artwork displays
- [ ] Background playback works
- [ ] App works on both iOS and Android emulators

---

## PHASE 2: Customization and Branding

**Goal:** Apply THRIVE branding throughout the app

**Duration:** 1-2 days

### Tasks
- [ ] Replace app icon with THRIVE logo
- [ ] Replace splash screen with THRIVE branding
- [ ] Update app name in configuration files
- [ ] Update color scheme to match brand
- [ ] Customize fonts if needed
- [ ] Update "About" screen content
- [ ] Add privacy policy and terms of service
- [ ] Remove/hide any Radio Online branding
- [ ] Customize player screen layout (if desired)
- [ ] Update any placeholder text

### Files to Modify
```
android/app/src/main/res/  -> Android icons
ios/Runner/Assets.xcassets/ -> iOS icons
lib/config/  -> Theme, colors, strings
assets/  -> Images, splash screen
pubspec.yaml  -> App name, fonts
```

### Verification Checklist
- [ ] App icon shows THRIVE logo
- [ ] Splash screen displays correctly
- [ ] Colors match brand guidelines
- [ ] No Radio Online branding visible
- [ ] All text is correct/updated

---

## PHASE 3: Supabase Authentication

**Goal:** Add login/registration requirement before accessing content

**Duration:** 1-3 days

### Tasks
- [ ] Add `supabase_flutter` package to pubspec.yaml
- [ ] Initialize Supabase in main.dart
- [ ] Create Login screen UI
- [ ] Create Registration screen UI
- [ ] Create Forgot Password screen UI
- [ ] Implement authentication logic
- [ ] Add auth state listener
- [ ] Create "auth gate" wrapper component
- [ ] Protect radio content behind auth check
- [ ] Add logout button to settings/profile
- [ ] Handle auth errors gracefully
- [ ] Test complete auth flow
- [ ] Add "Remember me" functionality

### New Screens to Create
```
lib/screens/auth/
  login_screen.dart
  register_screen.dart
  forgot_password_screen.dart
  auth_gate.dart
```

### Auth Gate Implementation
```dart
// auth_gate.dart
class AuthGate extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return StreamBuilder<AuthState>(
      stream: Supabase.instance.client.auth.onAuthStateChange,
      builder: (context, snapshot) {
        final session = snapshot.data?.session;
        if (session != null) {
          return RadioApp(); // Authenticated - show main app
        }
        return LoginScreen(); // Not authenticated - show login
      },
    );
  }
}
```

### Verification Checklist
- [ ] Cannot access radio without logging in
- [ ] Registration creates new user in Supabase
- [ ] Login works with valid credentials
- [ ] Login fails with invalid credentials (with error message)
- [ ] Forgot password sends reset email
- [ ] Logout returns to login screen
- [ ] Session persists after app restart
- [ ] Auth flow works on both iOS and Android

---

## PHASE 4: Future Enhancements (Post-Launch)

**Goal:** Add premium features after initial launch

### Potential Features
- [ ] CarPlay support (iOS)
- [ ] Android Auto support
- [ ] Subscription/payment integration (RevenueCat or Stripe)
- [ ] Push notifications
- [ ] Favorites sync across devices
- [ ] Direct AzuraCast API integration (auto-fetch now playing)
- [ ] Listening history
- [ ] User preferences/settings
- [ ] Sleep timer
- [ ] Alarm/wake feature

---

## Expected Project Structure

```
radio-online/
  android/                    # Android native files
  ios/                        # iOS native files
  lib/                        # Flutter/Dart source code
    config/                   # App configuration
    models/                   # Data models
    screens/                  # UI screens
      auth/                   # [PHASE 3] Login/Register
      home/                   # Station list
      player/                 # Audio player
      settings/               # App settings
    services/                 # API services
    widgets/                  # Reusable components
    main.dart                 # App entry point
  assets/                     # Images, fonts, etc.
  admin-panel/                # PHP admin panel (separate)
  pubspec.yaml                # Flutter dependencies
  README.md                   # Template documentation
```

---

## Environment Variables / Configuration

```
# Supabase (Phase 3)
SUPABASE_URL=https://your-project.supabase.co
SUPABASE_ANON_KEY=your-anon-key-here

# AzuraCast (Reference)
AZURACAST_URL=https://radio.yourdomain.com
AZURACAST_API_KEY=optional-if-needed

# App Config
APP_NAME=THRIVE Radio
BUNDLE_ID=com.THRIVE.radio
```

---

## Important Notes

### Radio Online Template
- **PHP Admin Panel Required:** The template includes a PHP admin panel that must be hosted on a web server
- **Database:** Admin panel uses MySQL/MariaDB
- **Firebase Optional:** Template may include Firebase integration for push notifications - can be disabled or replaced

### AzuraCast Compatibility
- **100% Compatible:** AzuraCast outputs standard Icecast/Shoutcast streams
- **Stream URLs:** Work directly in Radio Online without modification
- **Now Playing API:** For real-time metadata updates, consider Phase 4 direct API integration

### Supabase vs Firebase
- Radio Online may include Firebase by default
- You are replacing/supplementing with Supabase for auth
- Can keep Firebase for push notifications only, or replace entirely

### App Store Submission
- **iOS:** Requires Apple Developer account ($99/year)
- **Android:** Requires Google Play Developer account ($25 one-time)
- Both require review process (1-7 days)

---

## Quick Reference Commands

```bash
# Run Flutter app
flutter run

# Run on specific device
flutter run -d ios
flutter run -d android

# Build release APK
flutter build apk --release

# Build iOS
flutter build ios --release

# Get dependencies
flutter pub get

# Clean build
flutter clean && flutter pub get
```

---

## Useful Links

- Radio Online Template: https://codecanyon.net/item/radio-online/25624339
- AzuraCast API Docs: https://www.azuracast.com/docs/developers/apis/
- Supabase Flutter Docs: https://supabase.com/docs/reference/dart/introduction
- Flutter Documentation: https://docs.flutter.dev/

---

## Success Criteria

### Phase 1 Complete When:
- [ ] All 20 stations playable in emulator
- [ ] Admin panel accessible and functional
- [ ] No critical errors or crashes

### Phase 2 Complete When:
- [ ] App fully branded as THRIVE Radio
- [ ] No template placeholder content visible
- [ ] Visually ready for app store

### Phase 3 Complete When:
- [ ] Users must login to access radio
- [ ] Registration flow works end-to-end
- [ ] Auth state persists correctly
- [ ] Ready for beta testing

### Project Complete When:
- [ ] App submitted to App Store and Play Store
- [ ] Apps approved and live
- [ ] Users can download, register, and stream

---

*Document Version: 1.0*  
*Last Updated: December 2024*

