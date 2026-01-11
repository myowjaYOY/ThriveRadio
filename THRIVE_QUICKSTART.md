# THRIVE Radio - Quick Start Guide

## What You're Building

A branded radio streaming app using:
- **Template:** Radio Online (Flutter) - $39-59 from CodeCanyon
- **Streaming:** Your AzuraCast server (20 Barix streams)
- **Auth:** Supabase (your existing account)
- **Platforms:** iOS + Android

---

## The 3 Phases

| Phase | What | Time |
|-------|------|:----:|
| **1. Setup** | Get template running with your streams | 1-2 days |
| **2. Brand** | Customize logos, colors, text | 1-2 days |
| **3. Auth** | Add Supabase login requirement | 1-3 days |

---

## Phase 1 Checklist

```
[ ] Buy Radio Online: https://codecanyon.net/item/radio-online/25624339
[ ] Download and extract files
[ ] Set up Flutter environment
[ ] Host PHP admin panel
[ ] Run: flutter pub get
[ ] Run: flutter run
[ ] Access admin panel in browser
[ ] Add 20 stations (name + stream URL + artwork)
[ ] Test playback
```

### Getting Stream URLs from AzuraCast

1. Log into AzuraCast dashboard
2. Go to each station
3. Find "Stream URL" - usually like:
   ```
   https://radio.yourdomain.com/listen/station-name/radio.mp3
   ```
4. Copy into Radio Online admin panel

---

## Phase 2 Checklist

```
[ ] Replace app icons (1024x1024 PNG)
[ ] Replace splash screen
[ ] Update colors in lib/config/
[ ] Update app name in pubspec.yaml
[ ] Remove any "Radio Online" text
[ ] Add privacy policy URL
```

---

## Phase 3 Checklist

```
[ ] Add to pubspec.yaml:
    supabase_flutter: ^2.0.0

[ ] Run: flutter pub get

[ ] Initialize in main.dart:
    await Supabase.initialize(
      url: 'YOUR_SUPABASE_URL',
      anonKey: 'YOUR_ANON_KEY',
    );

[ ] Create login_screen.dart
[ ] Create register_screen.dart  
[ ] Create auth_gate.dart (blocks access until logged in)
[ ] Test full auth flow
```

---

## Key Commands

```bash
flutter pub get          # Install dependencies
flutter run              # Run in emulator
flutter run -d ios       # Run iOS specifically
flutter run -d android   # Run Android specifically
flutter clean            # Clean build cache
flutter build apk        # Build Android release
flutter build ios        # Build iOS release
```

---

## Your Credentials (Fill In)

```
SUPABASE_URL = ________________________
SUPABASE_ANON_KEY = ________________________
AZURACAST_URL = ________________________
```

---

## Need Help?

- Full details: See THRIVE_RADIO_PROJECT_BRIEF.md
- AzuraCast API: https://www.azuracast.com/docs/developers/apis/
- Supabase Flutter: https://supabase.com/docs/reference/dart/introduction
- Flutter docs: https://docs.flutter.dev/

