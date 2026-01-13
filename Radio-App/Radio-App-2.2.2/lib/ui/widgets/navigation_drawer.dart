import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:radio_online/auth/services/auth_service.dart';
import 'package:radio_online/cubits/landing_screen/landing_screen_cubit.dart';
import 'package:radio_online/cubits/player/player_cubit.dart';
import 'package:radio_online/ui/screens/favorite_screen.dart';
import 'package:radio_online/ui/screens/lang_bottom_sheet.dart';
import 'package:radio_online/ui/screens/legal_info_screen.dart';
import 'package:radio_online/ui/widgets/ad_widgets/ad_interstitial.dart';
import 'package:radio_online/utility/app_info.dart';
import 'package:radio_online/utility/app_languages.dart';
import 'package:radio_online/utility/app_localization.dart';
import 'package:radio_online/utility/constants.dart';
import 'package:share_plus/share_plus.dart';
import 'package:url_launcher/url_launcher.dart';

class CustomNavigationDrawer extends StatefulWidget {
  const CustomNavigationDrawer({super.key});

  @override
  State<CustomNavigationDrawer> createState() => _CustomNavigationDrawerState();
}

class _CustomNavigationDrawerState extends State<CustomNavigationDrawer> {
  late int _selectedIndex;

  @override
  void initState() {
    super.initState();
    AdInterstitial.load();
    _selectedIndex = context.read<LandingScreenCubit>().state.id;
  }

  @override
  Widget build(BuildContext context) {
    return Drawer(
      width: 304, // Given in material guidelines
      child: Column(
        children: [
          DrawerHeader(
            margin: EdgeInsetsDirectional.zero,
            padding: const EdgeInsetsDirectional.symmetric(horizontal: 16),
            decoration: BoxDecoration(color: Theme.of(context).primaryColor),
            child: SizedBox.expand(
              child: Image.asset(
                'assets/images/logo.png',
                fit: BoxFit.scaleDown,
              ),
            ),
          ),
          Expanded(
            child: ListView(
              padding: const EdgeInsetsDirectional.symmetric(vertical: 10),
              children: <Widget>[
                listItem(AppLocalizations.getTranslatedLabel(context, 'home'),
                    Icons.home, 0, () {
                  context.read<LandingScreenCubit>().loadHomeScreen();
                  Navigator.of(context).pop();
                }),
                // Category hidden - uncomment to restore
                // listItem(
                //     AppLocalizations.getTranslatedLabel(context, 'category'),
                //     Icons.category,
                //     1, () {
                //   context.read<LandingScreenCubit>().loadCategoryScreen();
                //   Navigator.of(context).pop();
                // }),
                if (AppInfo().cityMode)
                  listItem(AppLocalizations.getTranslatedLabel(context, 'city'),
                      Icons.location_city, 2, () {
                    context.read<LandingScreenCubit>().loadCityScreen();
                    Navigator.of(context).pop();
                  }),
                listItem(AppLocalizations.getTranslatedLabel(context, 'radio'),
                    Icons.radio, AppInfo().cityMode ? 3 : 2, () {
                  context.read<LandingScreenCubit>().loadRadioScreen();
                  Navigator.of(context).pop();
                }),
                listItem(
                    AppLocalizations.getTranslatedLabel(context, 'favourite'),
                    Icons.favorite,
                    4, () {
                  Navigator.of(context).push(
                    MaterialPageRoute<dynamic>(
                      builder: (_) => FavoriteScreen(
                        onTap: (index, allStations) {
                          context.read<PlayerCubit>().playRadioPlayList(
                              stationToBePlayed: allStations[index],
                              allStations: allStations);
                        },
                      ),
                    ),
                  );
                }),
                const Divider(),
                listItem(
                    AppLocalizations.getTranslatedLabel(context, 'languages'),
                    Icons.language,
                    5, () async {
                  Navigator.of(context).pop();
                  showLanguageModalBottomSheet(context, appLanguages);
                }),
                // Privacy Policy hidden - uncomment to restore
                // listItem(
                //     AppLocalizations.getTranslatedLabel(
                //       context,
                //       'privacy_policy',
                //     ),
                //     Icons.security,
                //     6, () {
                //   Navigator.of(context).push(
                //     MaterialPageRoute<dynamic>(
                //       builder: (context) => const LegalInfoScreen(
                //         legalInfo: 'privacy_policy',
                //         screenTitle: 'Privacy Policy',
                //       ),
                //     ),
                //   );
                // }),
                // Terms & Conditions hidden - uncomment to restore
                // listItem(
                //     AppLocalizations.getTranslatedLabel(
                //       context,
                //       'terms_conditions',
                //     ),
                //     Icons.warning,
                //     7, () {
                //   Navigator.of(context).push(
                //     MaterialPageRoute<dynamic>(
                //       builder: (context) => const LegalInfoScreen(
                //         legalInfo: 'terms_condition',
                //         screenTitle: 'Terms and Conditions',
                //       ),
                //     ),
                //   );
                // }),
                listItem(
                    AppLocalizations.getTranslatedLabel(context, 'about_us'),
                    Icons.info,
                    8, () {
                  Navigator.of(context).push(
                    MaterialPageRoute<dynamic>(
                      builder: (context) => const LegalInfoScreen(
                        legalInfo: 'about_us',
                        screenTitle: 'About Us',
                      ),
                    ),
                  );
                }),
                listItem(AppLocalizations.getTranslatedLabel(context, 'share'),
                    Icons.share, 9, () {
                  Share.share(shareAppText);
                }),
                listItem(
                    AppLocalizations.getTranslatedLabel(context, 'rate_us'),
                    Icons.star,
                    10, () {
                  launchUrl(Uri.parse(AppInfo().appLink));
                }),
                const Divider(),
                listItem(
                    AppLocalizations.getTranslatedLabel(context, 'logout'),
                    Icons.logout,
                    11, () async {
                  Navigator.of(context).pop(); // Close drawer first
                  await AuthService.instance.signOut();
                  // AuthGate will automatically redirect to welcome screen
                }),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget listItem(
    String title,
    IconData icon,
    int index,
    void Function() callback,
  ) {
    return ListTile(
      selectedColor: Theme.of(context).primaryColor,
      onTap: () {
        //not showing ads when selecting new language
        if (index != 5) {
          AdInterstitial.show();
        }
        callback();
        setState(() {
          _selectedIndex = index;
        });
      },
      title: Text(title),
      leading: Icon(icon, size: 20),
      selected: index == _selectedIndex,
    );
  }
}
