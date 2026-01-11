import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:radio_online/cubits/player/player_cubit.dart';
import 'package:radio_online/cubits/player/player_cubit_state.dart';
import 'package:radio_online/ui/widgets/report_dialog.dart';
import 'package:radio_online/utility/app_localization.dart';
import 'package:radio_online/utility/constants.dart';
import 'package:radio_online/utility/hive_utility.dart';
import 'package:share_plus/share_plus.dart';

class NowPlayingScreen extends StatefulWidget {
  const NowPlayingScreen({required this.playerCubit, super.key});

  final PlayerCubit playerCubit;

  @override
  State<NowPlayingScreen> createState() => _NowPlayingScreenState();
}

class _NowPlayingScreenState extends State<NowPlayingScreen> {
  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    final paddingWidth = MediaQuery.sizeOf(context).width * 0.05;

    return Scaffold(
      resizeToAvoidBottomInset: false,
      appBar: AppBar(
        backgroundColor: Theme.of(context).primaryColor,
        foregroundColor: Theme.of(context).colorScheme.onTertiary,
        centerTitle: true,
        title:
            Text(AppLocalizations.getTranslatedLabel(context, 'radio_online')),
        leading: IconButton(
          onPressed: Navigator.of(context).pop,
          icon: Icon(Icons.keyboard_arrow_down,
              size: 30, color: Theme.of(context).colorScheme.onTertiary),
        ),
      ),
      body: GestureDetector(
        onVerticalDragUpdate: (details) {
          if (details.delta.dy > 10) {
            ///Increase the delta to increase sensitivity
            Navigator.of(context).pop();
          }
        },
        child: BlocBuilder<PlayerCubit, PlayerCubitState>(
          bloc: widget.playerCubit,
          builder: (context, state) {
            if (widget.playerCubit.currentlyPlayingStation != null) {
              final isFavorite = HiveUtility.isFavoriteRadio(
                widget.playerCubit.currentlyPlayingStation!.radioStationId,
              );

              final imageSize = MediaQuery.sizeOf(context).width * 0.9;
              return Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Center(
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(16),
                      child: SizedBox(
                        width: imageSize,
                        height: imageSize,
                        child: Image.network(
                          widget.playerCubit.currentlyPlayingStation!.imageUrl,
                          fit: BoxFit.cover,
                          errorBuilder: (context, error, stack) {
                            return Image.asset(kFallbackImage);
                          },
                        ),
                      ),
                    ),
                  ),
                  Padding(
                    padding: EdgeInsetsDirectional.all(paddingWidth),
                    child: Row(
                      children: [
                        Expanded(
                          child: RichText(
                            maxLines: 3,
                            overflow: TextOverflow.ellipsis,
                            text: TextSpan(
                              children: [
                                TextSpan(
                                  ///need a better way for this
                                  text:
                                      '${widget.playerCubit.currentlyPlayingStation!.radioStationName}\n',
                                  style: Theme.of(context).textTheme.titleLarge,
                                ),
                                TextSpan(
                                  text: widget.playerCubit
                                      .currentlyPlayingStation!.description,
                                  style: Theme.of(context).textTheme.bodyLarge,
                                ),
                              ],
                            ),
                          ),
                        ),
                        IconButton(
                          onPressed: () {
                            if (isFavorite) {
                              HiveUtility.deleteFromFavorite(widget.playerCubit
                                  .currentlyPlayingStation!.radioStationId);
                            } else {
                              HiveUtility.addToFavorite(
                                widget.playerCubit.currentlyPlayingStation!,
                              );
                            }
                            setState(() {});
                          },
                          icon: Icon(
                            isFavorite
                                ? Icons.favorite_outlined
                                : Icons.favorite_outline,
                            color: Theme.of(context).primaryColor,
                            size: 30,
                          ),
                        ),
                      ],
                    ),
                  ),
                  Padding(
                    padding: EdgeInsetsDirectional.all(paddingWidth),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        IconButton(
                          onPressed: () => Share.share(
                            shareNowPlaying(
                              widget.playerCubit.currentlyPlayingStation!
                                  .radioStationName,
                            ),
                          ),
                          icon: const Icon(
                            Icons.share,
                            size: 30,
                          ),
                        ),
                        IconButton(
                            onPressed: widget.playerCubit.hasPreviousStation()
                                ? widget.playerCubit.playPrevious
                                : null,
                            icon: Icon(
                                Directionality.of(context).index == 0
                                    ? Icons.skip_next
                                    : Icons.skip_previous,
                                size: 35)),
                        if (state is PlayingState)
                          IconButton(
                            onPressed: widget.playerCubit.pauseRadio,
                            icon: const Icon(
                              Icons.pause,
                              size: 50,
                            ),
                          )
                        else if (state is StoppedState)
                          IconButton(
                            onPressed: widget.playerCubit.resumeRadio,
                            icon: const Icon(Icons.play_arrow, size: 50),
                          )
                        else if (state is LoadingState)
                          SizedBox(
                              height: 50,
                              width: 50,
                              child: const CircularProgressIndicator())
                        else if (state is FailureState || state is InitialState)
                          IconButton(
                            onPressed: () => widget.playerCubit.pauseRadio(),
                            icon: const Icon(Icons.play_arrow, size: 50),
                          ),
                        IconButton(
                          onPressed: widget.playerCubit.hasNextStation()
                              ? widget.playerCubit.playNext
                              : null,
                          icon: Icon(
                            Directionality.of(context).index == 0
                                ? Icons.skip_previous
                                : Icons.skip_next,
                            size: 35,
                          ),
                        ),
                        IconButton(
                          onPressed: () {
                            showDialog<AlertDialog>(
                              context: context,
                              builder: (_) => ReportDialog(
                                radioStationId: widget.playerCubit
                                    .currentlyPlayingStation!.radioStationId,
                              ),
                            );
                          },
                          icon: const Icon(
                            Icons.report,
                            size: 30,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              );
            } else {
              return const SizedBox.shrink();
            }
          },
        ),
      ),
    );
  }
}
