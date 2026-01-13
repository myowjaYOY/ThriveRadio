import 'dart:async';

import 'package:firebase_core/firebase_core.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'package:just_audio_background/just_audio_background.dart';
import 'package:radio_online/auth/config/supabase_config.dart';
import 'package:radio_online/cubits/app/app_settings_cubit.dart';
import 'package:radio_online/cubits/translation/local_lang_cubit.dart';
import 'package:radio_online/firebase_options.dart';
import 'package:radio_online/radio_online.dart';
import 'package:radio_online/utility/hive_utility.dart';
import 'package:radio_online/utility/notification_utility.dart';

void main() async {
  ///Unawaited is used on not so important actions during startup.
  ///As the app takes too much time to load at the default flutter splash screen

  WidgetsFlutterBinding.ensureInitialized();
  
  //for music player notification in android
  await JustAudioBackground.init(
    androidNotificationChannelId: 'com.ryanheise.bg_demo.channel.audio',
    androidNotificationChannelName: 'Audio playback',
    androidNotificationOngoing: true,
  );
  // TODO: Configure Firebase with your own project credentials
  // await Firebase.initializeApp(options: DefaultFirebaseOptions.currentPlatform);
  await HiveUtility.initializeHive();
  await initializeSupabase(); // Initialize Supabase for auth
  // NotificationUtility.initializeFirebaseMessaging();
  unawaited(MobileAds.instance.initialize());
  runApp(
    MultiBlocProvider(
      providers: [
        BlocProvider(
          create: (_) => AppSettingsCubit(),
        ),
        BlocProvider<LocaleCubit>(
          create: (context) => LocaleCubit(),
        ),
      ],
      child: const RadioOnline(),
    ),
  );
}
