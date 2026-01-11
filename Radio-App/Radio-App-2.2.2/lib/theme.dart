import 'package:flex_color_scheme/flex_color_scheme.dart';
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:radio_online/utility/app_info.dart';
import 'package:radio_online/utility/constants.dart';

// Initialize with defaults, will be updated when API settings load
ThemeData lightTheme = _buildLightTheme();
ThemeData darkTheme = _buildDarkTheme();

ThemeData _buildLightTheme() {
  return FlexThemeData.light(
    primary: AppInfo().lightPrimaryColor,
    background: AppInfo().lightBackgroundColor,
    usedColors: 1,
    subThemesData: const FlexSubThemesData(),
    textTheme: GoogleFonts.nunitoTextTheme().apply(
      displayColor: const Color(0xff121212),
      bodyColor: const Color(0xff121212),
      decorationColor: const Color(0xff121212),
    ),
    visualDensity: VisualDensity.comfortable,
    useMaterial3: true,
    blendLevel: 20,
    surfaceMode: FlexSurfaceMode.highBackgroundLowScaffold,
  );
}

ThemeData _buildDarkTheme() {
  return FlexThemeData.dark(
    primary: AppInfo().lightPrimaryColor,
    background: AppInfo().lightBackgroundColor,
    usedColors: 1,
    subThemesData: const FlexSubThemesData(),
    visualDensity: VisualDensity.comfortable,
    textTheme: GoogleFonts.nunitoTextTheme(),
    useMaterial3: true,
    blendLevel: 20,
    surfaceMode: FlexSurfaceMode.highBackgroundLowScaffold,
  );
}

void generateTheme() {
  lightTheme = FlexThemeData.light(
    primary: AppInfo().lightPrimaryColor,
    background: AppInfo().lightBackgroundColor,
    usedColors: 1,
    subThemesData: const FlexSubThemesData(),
    textTheme: GoogleFonts.nunitoTextTheme().apply(
      displayColor: const Color(0xff121212),
      bodyColor: const Color(0xff121212),
      decorationColor: const Color(0xff121212),
    ),
    visualDensity: VisualDensity.comfortable,
    useMaterial3: true,
    blendLevel: 20,
    surfaceMode: FlexSurfaceMode.highBackgroundLowScaffold,
  );

  darkTheme = FlexThemeData.dark(
    ///Update to dark primary color when available in admin panel
    primary: AppInfo().lightPrimaryColor,
    background: AppInfo().lightBackgroundColor,
    usedColors: 1,
    subThemesData: const FlexSubThemesData(),
    visualDensity: VisualDensity.comfortable,
    textTheme: GoogleFonts.nunitoTextTheme(),
    useMaterial3: true,
    blendLevel: 20,
    surfaceMode: FlexSurfaceMode.highBackgroundLowScaffold,

    ///Uncomment this to enforce pure black background in dark mode
    //darkIsTrueBlack: true
  );
}
