import 'dart:async';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:radio_online/cubits/landing_screen/landing_screen_cubit.dart';
import 'package:radio_online/cubits/landing_screen/landing_screen_state.dart';
import 'package:radio_online/ui/widgets/home_screen_widgets/favorites_grid.dart';
import 'package:radio_online/ui/widgets/home_screen_widgets/list_view_section.dart';
import 'package:radio_online/ui/widgets/home_screen_widgets/radio_station_carousel.dart';
import 'package:radio_online/utility/app_localization.dart';
import 'package:radio_online/utility/error_message_labels.dart';
import 'package:radio_online/utility/error_widget.dart';

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    Timer? timer;
    final size = MediaQuery.sizeOf(context);

    return PopScope(
      canPop: false,
      onPopInvoked: (didPop) {
        if (didPop) return;

        ///BackButtonListener is an option but required Router Ancestor instead of Navigator
        if (Scaffold.of(context).isDrawerOpen) {
          Scaffold.of(context).closeDrawer();
        } else {
          if (timer != null) {
            SystemNavigator.pop();
            exit(0);
          } else {
            timer = Timer(const Duration(seconds: 1), () {
              timer = null;
            });
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content:
                    Text(AppLocalizations.getTranslatedLabel(context, 'exit')),
                duration: const Duration(seconds: 1),
              ),
            );
          }
        }
      },
      child: RefreshIndicator(
        onRefresh: context.read<LandingScreenCubit>().refreshState,
        child: SizedBox(
          width: size.width,
          height: size.height,
          child: BlocBuilder<LandingScreenCubit, LandingScreenState>(
            buildWhen: (previousState, currentState) =>
                currentState is HomeScreenState,
            builder: (context, state) {
              state = state as HomeScreenState;
              switch (state.status) {
                case Status.initial:
                  context.read<LandingScreenCubit>().loadHomeScreen();
                  return const Center(child: CircularProgressIndicator());
                case Status.loading:
                  return const Center(child: CircularProgressIndicator());
                case Status.loaded:
                  return SingleChildScrollView(
                    controller: ScrollController(),
                    physics: const AlwaysScrollableScrollPhysics(),
                    child: Column(
                      children: [
                        RadioStationCarousel(sliders: state.sliders!),
                        ListViewSection(
                          title: AppLocalizations.getTranslatedLabel(
                              context, 'category'),
                          listData: state.categoryList!,
                          callback: () {
                            context
                                .read<LandingScreenCubit>()
                                .loadCategoryScreen();
                          },
                        ),
                        ListViewSection(
                          title: AppLocalizations.getTranslatedLabel(
                              context, 'latest'),
                          listData: state.latestList!,
                          callback: () {
                            context
                                .read<LandingScreenCubit>()
                                .loadRadioScreen();
                          },
                        ),
                        const FavoritesGrid(),
                      ],
                    ),
                  );
                case Status.error:
                  final isNoInternet = state.errorMessage!.contains(noInternet);

                  return QErrorWidget(
                    onTapRetry:
                        context.read<LandingScreenCubit>().loadHomeScreen,
                    showTryAgain: isNoInternet,
                    error: isNoInternet
                        ? AppLocalizations.getTranslatedLabel(
                            context, 'internet')
                        : state.errorMessage ??
                            AppLocalizations.getTranslatedLabel(
                                context, 'city_retrieval'),
                  );
              }
            },
          ),
        ),
      ),
    );
  }
}
