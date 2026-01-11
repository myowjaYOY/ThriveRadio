import 'dart:developer';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:radio_online/cubits/player/player_cubit.dart';
import 'package:radio_online/cubits/player/player_cubit_state.dart';
import 'package:radio_online/ui/screens/now_playing_screen.dart';
import 'package:radio_online/utility/app_localization.dart';
import 'package:radio_online/utility/constants.dart';
import 'package:radio_online/utility/hive_utility.dart';

class NowPlayingSheet extends StatefulWidget {
  const NowPlayingSheet({super.key});

  @override
  State<NowPlayingSheet> createState() => _NowPlayingSheetState();
}

class _NowPlayingSheetState extends State<NowPlayingSheet> {
  late final PlayerCubit playerCubit;

  @override
  void initState() {
    super.initState();
    playerCubit = context.read<PlayerCubit>();
    HiveUtility.addFavoriteListener(() {
      setState(() {});
    });
  }

  @override
  Widget build(BuildContext context) {
    return BlocConsumer<PlayerCubit, PlayerCubitState>(
      listener: (context, state) {
        if (state is FailureState) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                AppLocalizations.getTranslatedLabel(context, 'went_wrong'),
              ),
              duration: const Duration(seconds: 1),
            ),
          );
        }
      },
      builder: (context, state) {
        if (playerCubit.currentlyPlayingStation != null) {
          final isFavorite = HiveUtility.isFavoriteRadio(
            playerCubit.currentlyPlayingStation!.radioStationId,
          );
          return GestureDetector(
            onTap: () {
              Navigator.of(context).push(
                PageRouteBuilder<dynamic>(
                  pageBuilder: (context, _, __) => NowPlayingScreen(
                    playerCubit: playerCubit,
                  ),
                  transitionsBuilder: (context, animation, sanimation, child) =>
                      SlideTransition(
                    position: Tween<Offset>(
                      begin: const Offset(0, 1),
                      end: Offset.zero,
                    ).animate(
                      CurvedAnimation(
                        parent: animation,
                        curve: Curves.easeIn,
                      ),
                    ),
                    child: child,
                  ),
                ),
              );
            },
            child: Container(
              width: MediaQuery.sizeOf(context).width,
              padding: const EdgeInsetsDirectional.all(8),
              color: Theme.of(context).primaryColor,
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  SizedBox(
                    height: 50,
                    width: 50,
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(50),
                      child: Image.network(
                        fit: BoxFit.fill,
                        playerCubit.currentlyPlayingStation!.imageUrl,
                        errorBuilder: (context, error, stack) {
                          log('error occurred. loading fallback image');
                          return Image.asset(
                            kFallbackImage,

                            ///Image becomes invisible in case this is removed
                            color: Theme.of(context).colorScheme.onPrimary,
                          );
                        },
                      ),
                    ),
                  ),
                  Expanded(
                    child: Container(
                      margin: const EdgeInsetsDirectional.only(start: 8),
                      child: Text(
                        playerCubit.currentlyPlayingStation!.radioStationName,
                        style: Theme.of(context)
                            .textTheme
                            .titleMedium
                            ?.copyWith(
                              fontWeight: FontWeight.w600,
                              color: Theme.of(context).colorScheme.onTertiary,
                            ),
                      ),
                    ),
                  ),
                  if (state is PlayingState)
                    IconButton(
                      onPressed: playerCubit.pauseRadio,
                      icon: Icon(
                        Icons.pause,
                        size: 35,
                        color: Theme.of(context).colorScheme.onTertiary,
                      ),
                    )
                  else if (state is StoppedState)
                    IconButton(
                      onPressed: () {
                        if (state.isConnected) {
                          playerCubit.resumeRadio();
                        } else {
                          ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(
                              backgroundColor: kPrimaryColor,
                              content: Text(
                                AppLocalizations.getTranslatedLabel(
                                    context, 'internet'),
                              ),
                            ),
                          );
                        }
                      },
                      icon: Icon(
                        Icons.play_arrow,
                        size: 35,
                        color: Theme.of(context).colorScheme.onTertiary,
                      ),
                    )
                  else if (state is LoadingState)
                    CircularProgressIndicator(
                      color: Theme.of(context).colorScheme.onTertiary,
                    )
                  else if (state is FailureState || state is InitialState)
                    // const CircularProgressIndicator(),
                    IconButton(
                      onPressed: () => playerCubit.pauseRadio(),
                      icon: Icon(
                        Icons.play_arrow,
                        size: 35,
                        color: Theme.of(context).colorScheme.onTertiary,
                      ),
                    ),
                  IconButton(
                    onPressed: () {
                      if (isFavorite) {
                        HiveUtility.deleteFromFavorite(
                          playerCubit.currentlyPlayingStation!.radioStationId,
                        );
                      } else {
                        HiveUtility.addToFavorite(
                            playerCubit.currentlyPlayingStation!);
                      }
                    },
                    icon: Icon(
                      isFavorite
                          ? Icons.favorite_outlined
                          : Icons.favorite_outline,
                      size: 25,
                      color: Theme.of(context).colorScheme.onTertiary,
                    ),
                  ),
                ],
              ),
            ),
          );
        } else {
          return const SizedBox.shrink();
        }
      },
    );
  }
}
