import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:radio_online/cubits/landing_screen/landing_screen_cubit.dart';
import 'package:radio_online/cubits/landing_screen/landing_screen_state.dart';
import 'package:radio_online/cubits/player/player_cubit.dart';
import 'package:radio_online/ui/screens/category_screen.dart';
import 'package:radio_online/ui/screens/city_screen.dart';
import 'package:radio_online/ui/screens/force_app_update_screen.dart';
import 'package:radio_online/ui/screens/home_screen.dart';
import 'package:radio_online/ui/screens/radio_screen.dart';
import 'package:radio_online/ui/widgets/bottom_navigation.dart';
import 'package:radio_online/ui/widgets/home_screen_widgets/expandable_search_bar.dart';
import 'package:radio_online/ui/widgets/navigation_drawer.dart';
import 'package:radio_online/ui/widgets/now_playing_sheet.dart';
import 'package:radio_online/utility/app_localization.dart';
import 'package:radio_online/utility/app_utilities.dart';

class LandingScreen extends StatefulWidget {
  const LandingScreen({super.key});

  @override
  State<LandingScreen> createState() => _LandingScreenState();
}

class _LandingScreenState extends State<LandingScreen> {
  late bool showForceAppUpdate = false;

  @override
  void initState() {
    super.initState();
    checkForUpdates();
  }

  Future<void> checkForUpdates() async {
    await Future<void>.delayed(Duration.zero);
    final forceUpdate = await AppUtilities.shouldForceAppUpdate();

    if (forceUpdate) {
      setState(() => showForceAppUpdate = true);
    }
  }

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.sizeOf(context);

    if (showForceAppUpdate) {
      return const ForceAppUpdateScreen();
    }

    return MultiBlocProvider(
      providers: [
        BlocProvider(create: (_) => LandingScreenCubit()),
        BlocProvider(create: (_) => PlayerCubit()),
      ],
      child: Scaffold(
        appBar: AppBar(
          iconTheme: Theme.of(context).iconTheme.copyWith(
                color: Colors.white,
              ),
          backgroundColor: Theme.of(context).primaryColor,
          foregroundColor: Colors.white,
          centerTitle: true,
          title: Text(
              AppLocalizations.getTranslatedLabel(context, 'radio_online')),
          actions: const [ExpandableSearchBar()],
        ),
        drawer: const CustomNavigationDrawer(),
        bottomNavigationBar: const BottomNavigation(),
        body: SizedBox(
          height: size.height,
          width: size.width,
          child: Column(
            children: [
              Expanded(
                child: BlocBuilder<LandingScreenCubit, LandingScreenState>(
                  builder: (context, state) {
                    switch (state) {
                      case HomeScreenState():
                        return const HomeScreen();
                      case CityScreenState():
                        return const CityScreen();
                      case CategoryScreenState():
                        return const CategoryScreen();
                      case RadioScreenState():
                        return const RadioScreen();
                      default:
                        return const SizedBox.shrink();
                    }
                  },
                ),
              ),
              const NowPlayingSheet(),
            ],
          ),
        ),
      ),
    );
  }
}
