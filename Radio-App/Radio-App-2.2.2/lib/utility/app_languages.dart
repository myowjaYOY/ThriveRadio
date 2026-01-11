//by default language of the app

import 'package:radio_online/models/app_language.dart';

const String defaultLanguageCode = 'en';

//Add language code in this list
//visit this to find languageCode for your respective language
//https://developers.google.com/admin-sdk/directory/v1/languages
const List<AppLanguage> appLanguages = [
  //Please add language code here and language name
  AppLanguage(languageCode: 'en', languageName: 'English', countryCode: 'US'),
  AppLanguage(languageCode: 'hi', languageName: 'Hindi', countryCode: 'IN'),
  AppLanguage(languageCode: 'ar', languageName: 'Arabic', countryCode: 'AE'),
  // AppLanguage(languageCode: "ur", languageName: "اردو - Urdu"),
];
