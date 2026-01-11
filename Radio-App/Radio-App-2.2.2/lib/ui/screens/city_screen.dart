import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:radio_online/cubits/landing_screen/landing_screen_cubit.dart';
import 'package:radio_online/cubits/landing_screen/landing_screen_state.dart';
import 'package:radio_online/utility/app_localization.dart';
import 'package:radio_online/utility/error_message_labels.dart';
import 'package:radio_online/utility/error_widget.dart';

class CityScreen extends StatelessWidget {
  const CityScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.sizeOf(context);

    return PopScope(
      canPop: false,
      onPopInvoked: (didPop) {
        if (didPop) return;
        if (Scaffold.of(context).isDrawerOpen) {
          Scaffold.of(context).closeDrawer();
        } else {
          context.read<LandingScreenCubit>().loadHomeScreen();
        }
      },
      child: SizedBox(
        width: size.width,
        height: size.height,
        child: BlocBuilder<LandingScreenCubit, LandingScreenState>(
          builder: (context, state) {
            final cityState = state as CityScreenState;
            switch (cityState.status) {
              case Status.initial:
                return const Center(child: CircularProgressIndicator());
              case Status.loading:
                return const Center(child: CircularProgressIndicator());
              case Status.error:
                final isNoInternet = state.errorMessage!.contains(noInternet);

                return QErrorWidget(
                  onTapRetry: context.read<LandingScreenCubit>().loadCityScreen,
                  showTryAgain: isNoInternet,
                  error: isNoInternet
                      ? AppLocalizations.getTranslatedLabel(context, 'internet')
                      : state.errorMessage ??
                          AppLocalizations.getTranslatedLabel(
                              context, 'city_retrieval'),
                );
              case Status.loaded:
                final data = cityState.cityList!;
                final itemCount = data.length;
                return Padding(
                  padding: const EdgeInsetsDirectional.symmetric(horizontal: 8),
                  child: NotificationListener<ScrollNotification>(
                    onNotification: (notification) {
                      if (notification is ScrollEndNotification &&
                          notification.metrics.pixels ==
                              notification.metrics.maxScrollExtent &&
                          (state.hasMoreData ?? false)) {
                        context.read<LandingScreenCubit>().loadMoreCities();
                      }
                      return false;
                    },
                    child: RefreshIndicator(
                      onRefresh:
                          context.read<LandingScreenCubit>().refreshState,
                      child: ListView.builder(
                        controller: ScrollController(),
                        padding: const EdgeInsets.symmetric(vertical: 8),
                        physics: const BouncingScrollPhysics(),
                        itemCount: itemCount + 1,
                        itemBuilder: (context, index) {
                          if (index == itemCount) {
                            if (state.hasMoreData ?? false) {
                              return const Center(
                                child: CircularProgressIndicator(),
                              );
                            } else {
                              return const SizedBox.shrink();
                            }
                          }
                          return GestureDetector(
                            onTap: () {
                              context
                                  .read<LandingScreenCubit>()
                                  .loadRadioScreenByCity(
                                    cityId: data[index].cityId,
                                    cityName: data[index].cityName,
                                  );
                            },
                            child: Card(
                              child: Container(
                                margin: const EdgeInsetsDirectional.symmetric(
                                  vertical: 22,
                                  horizontal: 14,
                                ),
                                width: size.width,
                                child: Row(
                                  children: [
                                    Text(
                                      data[index].cityName,
                                      style: Theme.of(context)
                                          .textTheme
                                          .titleMedium
                                          ?.copyWith(
                                            fontWeight: FontWeight.w600,
                                          ),
                                    ),
                                    Expanded(child: Container()),
                                    const Icon(Icons.location_city),
                                  ],
                                ),
                              ),
                            ),
                          );
                        },
                      ),
                    ),
                  ),
                );
            }
          },
        ),
      ),
    );
  }
}
