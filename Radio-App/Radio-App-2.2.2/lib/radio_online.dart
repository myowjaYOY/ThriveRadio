import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:radio_online/auth/widgets/auth_gate.dart';
import 'package:radio_online/cubits/app/app_settings_cubit.dart';
import 'package:radio_online/cubits/theme_mode/theme_mode_cubit.dart';
import 'package:radio_online/cubits/translation/local_lang_cubit.dart';
import 'package:radio_online/repository/radio_station_repo.dart';
import 'package:radio_online/theme.dart';
import 'package:radio_online/ui/screens/landing_screen.dart';
import 'package:radio_online/ui/screens/maintenance_screen.dart';
import 'package:radio_online/ui/screens/splash_screen.dart';
import 'package:radio_online/utility/app_info.dart';
import 'package:radio_online/utility/app_languages.dart';
import 'package:radio_online/utility/app_localization.dart';
import 'package:radio_online/utility/error_message_labels.dart';
import 'package:radio_online/utility/error_screen.dart';
import 'package:radio_online/utility/hive_utility.dart';
import 'package:radio_online/utility/notification_utility.dart';

class RadioOnline extends StatefulWidget {
  const RadioOnline({super.key});

  @override
  State<RadioOnline> createState() => _RadioOnlineState();
}

class _RadioOnlineState extends State<RadioOnline> {
  @override
  void initState() {
    super.initState();
    fetchAppSettings();
    // TODO: Enable once Firebase is configured
    // initializeFirebaseMessaging();
  }

  void fetchAppSettings() {
    context.read<AppSettingsCubit>().fetchAppSettings();
  }

  Future<void> initializeFirebaseMessaging() async {
    final permissionGiven = await NotificationUtility.requestPermission();
    if (permissionGiven) {
      await FirebaseMessaging.instance.getToken().then((token) async {
        if (token != null) {
          await RadioStationRepo().registerToken(token);
        }
      }).onError((error, stackTrace) => null);
    }
  }

  @override
  Widget build(BuildContext context) {
    return BlocConsumer<AppSettingsCubit, AppSettingsState>(
      builder: (context, state) {
        return switch (state.status) {
          AppSettingsStatus.failure => ErrorScreen(
              onTapRetry: fetchAppSettings,
              error: state.error!.contains(noInternet)
                  ? "no internet"
                  : state.error!,
              showTryAgain: state.error!.contains(noInternet),
            ),
          AppSettingsStatus.success => AppInfo().isAppMaintenanceEnabled
              ? const MaintenanceScreen()
              : BlocProvider(
                  create: (_) => ThemeModeCubit(HiveUtility.themeMode),
                  child: BlocBuilder<LocaleCubit, Locale>(
                    builder: (context, localeState) {
                      return MaterialApp(
                        theme: lightTheme,
                        localizationsDelegates: const [
                          AppLocalizations.delegate,
                          GlobalMaterialLocalizations.delegate,
                          GlobalWidgetsLocalizations.delegate,
                          GlobalCupertinoLocalizations.delegate,
                        ],
                        locale: localeState,
                        supportedLocales: appLanguages
                            .map<Locale>(
                                (e) => Locale(e.languageCode, e.countryCode))
                            .toList(),
                        darkTheme: darkTheme,
                        themeMode: ThemeMode.light,
                        home: const AuthGate(child: LandingScreen()),
                        debugShowCheckedModeBanner: false,
                      );
                    },
                  ),
                ),
          _ => const SplashScreen(),
        };
      },
      listener: (_, state) {
        if (state.status == AppSettingsStatus.success) {
          generateTheme();
        }
      },
    );
  }
}
