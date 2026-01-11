import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:radio_online/cubits/landing_screen/landing_screen_cubit.dart';
import 'package:radio_online/cubits/landing_screen/landing_screen_state.dart';
import 'package:radio_online/ui/widgets/radio_screen_widgets/radio_station_list.dart';
import 'package:radio_online/utility/app_localization.dart';
import 'package:radio_online/utility/error_message_labels.dart';
import 'package:radio_online/utility/error_widget.dart';

class RadioScreen extends StatelessWidget {
  const RadioScreen({super.key});

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
          context.read<LandingScreenCubit>().loadPreviousState();
        }
      },
      child: SizedBox(
        width: size.width,
        height: size.height,
        child: BlocBuilder<LandingScreenCubit, LandingScreenState>(
          buildWhen: (prevState, currState) => currState is RadioScreenState,
          builder: (context, state) {
            final radioState = state as RadioScreenState;
            switch (radioState.status) {
              case Status.initial:
                context.read<LandingScreenCubit>().loadRadioScreen();
                return const SizedBox.shrink();
              case Status.loading:
                return const Center(child: CircularProgressIndicator());
              case Status.error:
                final isNoInternet = state.errorMessage!.contains(noInternet);

                return QErrorWidget(
                  onTapRetry:
                      context.read<LandingScreenCubit>().loadRadioScreen,
                  showTryAgain: isNoInternet,
                  error: isNoInternet
                      ? AppLocalizations.getTranslatedLabel(context, 'internet')
                      : state.errorMessage ??
                          AppLocalizations.getTranslatedLabel(
                              context, 'went_wrong'),
                );
              case Status.loaded:
                return Padding(
                  padding: const EdgeInsetsDirectional.symmetric(horizontal: 8),
                  child: Column(
                    children: [
                      if (state.filterName != null)
                        Row(
                          children: [
                            Expanded(
                              child: Text(
                                "Search Results for '${state.filterName}'",
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                            IconButton(
                              onPressed: context
                                  .read<LandingScreenCubit>()
                                  .loadRadioScreen,
                              icon: const Icon(Icons.close),
                            ),
                          ],
                        ),
                      Expanded(
                        child: RadioStationList(
                          data: state.radioList ?? [],
                          hasMoreData: (state.hasMoreData ?? false) &&
                              state.filterName == null,
                        ),
                      ),
                    ],
                  ),
                );
            }
          },
        ),
      ),
    );
  }
}
