# THRIVE Radio - Supabase Authentication Design

> **Status:** Ready for Implementation  
> **Created:** December 8, 2024  
> **Phase:** 3 - Authentication

---

## Overview

Add Supabase authentication to require users to log in before accessing the radio app. This creates a paywall - only authenticated users can listen to streams.

---

## Requirements Summary

| Requirement | Decision |
|-------------|----------|
| Login required to access radio | ✅ Yes |
| Email/Password authentication | ✅ Yes |
| Strong password constraints | ✅ Yes (Supabase default) |
| Email verification | ❌ No - Not required |
| User self-registration | ❌ No - Admin creates accounts |
| Social login (Google/Apple) | ❌ No |
| Forgot password flow | ⏸️ Delayed - Next phase |
| Remember session | ✅ Yes - Persist across app restarts |

---

## Architecture

### High-Level Flow

```
┌─────────────────────────────────────────────────────────────┐
│                      APP LAUNCH                              │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
              ┌─────────────────────────┐
              │  Check: User Logged In? │
              │  (Supabase Session)     │
              └─────────────────────────┘
                     │           │
            ┌────────┘           └────────┐
            ▼                             ▼
     ┌─────────────┐              ┌─────────────────┐
     │   YES       │              │      NO         │
     │   ↓         │              │      ↓          │
     │ Show Radio  │              │ Show Login      │
     │    App      │              │   Screen        │
     └─────────────┘              └─────────────────┘
                                          │
                                          ▼
                                  ┌───────────────┐
                                  │ User Enters   │
                                  │ Email/Password│
                                  └───────────────┘
                                          │
                                          ▼
                                  ┌───────────────┐
                                  │   LOGIN       │
                                  │   Success?    │
                                  └───────────────┘
                                    │         │
                               YES ▼         ▼ NO
                          ┌──────────┐  ┌──────────┐
                          │ Show     │  │ Show     │
                          │ Radio App│  │ Error    │
                          └──────────┘  └──────────┘
```

---

## Components to Create

### 1. Auth Gate (`lib/screens/auth/auth_gate.dart`)
- Root-level wrapper widget
- Listens to `Supabase.instance.client.auth.onAuthStateChange`
- Returns `LoginScreen` if no session
- Returns `RadioOnline` (main app) if session exists

### 2. Login Screen (`lib/screens/auth/login_screen.dart`)
- Email input field
- Password input field (obscured)
- Login button
- Error message display
- Loading state during authentication
- **NO** registration link (admin-only account creation)
- **NO** forgot password link (next phase)

### 3. Auth Service (`lib/services/auth_service.dart`)
- `signIn(email, password)` - Authenticate user
- `signOut()` - Clear session
- `getCurrentUser()` - Get current user info
- `isAuthenticated` - Check if user is logged in

### 4. Logout Button
- Add to navigation drawer
- Calls `signOut()` and returns to login screen

---

## File Structure

```
lib/
├── main.dart                    # Initialize Supabase here
├── screens/
│   └── auth/
│       ├── auth_gate.dart       # NEW - Auth wrapper
│       └── login_screen.dart    # NEW - Login UI
├── services/
│   └── auth_service.dart        # NEW - Supabase auth logic
└── utility/
    └── constants.dart           # Add Supabase credentials
```

---

## Login Screen UI Design

```
┌─────────────────────────────────────┐
│                                     │
│         🎵 THRIVE Radio             │
│         ───────────────             │
│              (Logo)                 │
│                                     │
│                                     │
│  ┌───────────────────────────────┐  │
│  │ 📧 Email                      │  │
│  └───────────────────────────────┘  │
│                                     │
│  ┌───────────────────────────────┐  │
│  │ 🔒 Password                   │  │
│  └───────────────────────────────┘  │
│                                     │
│                                     │
│  ┌───────────────────────────────┐  │
│  │         SIGN IN               │  │
│  └───────────────────────────────┘  │
│                                     │
│         [Error message here]        │
│                                     │
└─────────────────────────────────────┘
```

### UI Specifications
- Use app's primary color scheme (maroon/pink)
- Logo at top center
- Clean, minimal design
- Clear error messages for failed login
- Loading spinner on button during auth

---

## Supabase Configuration

### Required Credentials
```dart
// Add to lib/utility/constants.dart

const supabaseUrl = 'YOUR_SUPABASE_PROJECT_URL';
const supabaseAnonKey = 'YOUR_SUPABASE_ANON_KEY';
```

### Flutter Package
```yaml
# Add to pubspec.yaml

dependencies:
  supabase_flutter: ^2.0.0
```

### Initialize in main.dart
```dart
void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  await Supabase.initialize(
    url: supabaseUrl,
    anonKey: supabaseAnonKey,
  );
  
  // ... rest of initialization
}
```

---

## Password Constraints

Using Supabase default strong password requirements:
- Minimum 6 characters
- (Can be customized in Supabase Dashboard → Authentication → Policies)

---

## Session Persistence

Supabase Flutter SDK automatically:
- Stores session token securely
- Refreshes tokens when needed
- Persists across app restarts
- No additional code needed

---

## Error Handling

| Error | User Message |
|-------|--------------|
| Invalid credentials | "Invalid email or password" |
| Network error | "Connection error. Please try again." |
| User not found | "Invalid email or password" (same as wrong password for security) |
| Too many attempts | "Too many login attempts. Please wait." |

---

## User Management

Since self-registration is disabled:

### How to Create Users
1. Go to Supabase Dashboard
2. Navigate to Authentication → Users
3. Click "Add User"
4. Enter email and password
5. User can now log in via the app

### Alternative: Invite Users via Email
1. Supabase Dashboard → Authentication → Users
2. Click "Invite"
3. Enter email address
4. User receives email with login link

---

## Implementation Checklist

- [ ] Add `supabase_flutter` to pubspec.yaml
- [ ] Add Supabase credentials to constants.dart
- [ ] Initialize Supabase in main.dart
- [ ] Create `auth_service.dart`
- [ ] Create `login_screen.dart`
- [ ] Create `auth_gate.dart`
- [ ] Wrap app with AuthGate
- [ ] Add logout button to drawer
- [ ] Test login flow
- [ ] Test session persistence
- [ ] Test logout flow

---

## Future Enhancements (Next Phase)

- [ ] Forgot password flow
- [ ] Email verification (optional)
- [ ] Social login (Google/Apple)
- [ ] User profile screen
- [ ] Change password functionality

---

## Dependencies on You

Before implementation, please provide:

1. **Supabase Project URL**  
   `https://xxxxx.supabase.co`

2. **Supabase Anon Key**  
   `eyJhbGciOiJIUzI1...`

Find these at: Supabase Dashboard → Settings → API

---

*Document ready for implementation when you are!*

