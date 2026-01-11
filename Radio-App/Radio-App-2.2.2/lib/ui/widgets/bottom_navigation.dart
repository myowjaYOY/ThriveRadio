import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:radio_online/cubits/landing_screen/landing_screen_cubit.dart';
import 'package:radio_online/utility/app_info.dart';
import 'package:radio_online/utility/app_localization.dart';

class BottomNavigation extends StatefulWidget {
  const BottomNavigation({super.key});

  @override
  State<BottomNavigation> createState() => _BottomNavigationState();
}

class _BottomNavigationState extends State<BottomNavigation> {
  int _selectedIndex = 0;
  bool hasClicked = false;

  @override
  void initState() {
    super.initState();
    context.read<LandingScreenCubit>().stream.listen((event) {
      if (_selectedIndex != event.id) {
        setState(() {
          _selectedIndex = event.id;
        });
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return BottomNavigationBar(
      currentIndex: _selectedIndex,
      items: [
        BottomNavigationBarItem(
          icon: const Icon(Icons.home),
          label: AppLocalizations.getTranslatedLabel(context, 'home'),
        ),
        BottomNavigationBarItem(
          icon: const Icon(Icons.category),
          label: AppLocalizations.getTranslatedLabel(context, 'category'),
        ),
        if (AppInfo().cityMode)
          BottomNavigationBarItem(
            icon: const Icon(Icons.location_city),
            label: AppLocalizations.getTranslatedLabel(context, 'city'),
          ),
        BottomNavigationBarItem(
          icon: const Icon(Icons.radio),
          label: AppLocalizations.getTranslatedLabel(context, 'radio'),
        ),
      ],
      onTap: (index) {
        switch (index) {
          case 0:
            context.read<LandingScreenCubit>().loadHomeScreen();
          case 1:
            context.read<LandingScreenCubit>().loadCategoryScreen();
          case 2:
            if (AppInfo().cityMode) {
              context.read<LandingScreenCubit>().loadCityScreen();
            } else {
              context.read<LandingScreenCubit>().loadRadioScreen();
            }
          case 3:
            context.read<LandingScreenCubit>().loadRadioScreen();
        }
        if (_selectedIndex != index) {
          setState(() {
            _selectedIndex = index;
          });
        }
      },
    );
  }
}
