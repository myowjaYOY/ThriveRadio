import 'dart:developer';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:radio_online/cubits/landing_screen/landing_screen_cubit.dart';
import 'package:radio_online/cubits/player/player_cubit.dart';
import 'package:radio_online/models/radio_station.dart';
import 'package:radio_online/ui/widgets/ad_widgets/ad_mob_banner.dart';
import 'package:radio_online/utility/app_localization.dart';
import 'package:radio_online/utility/constants.dart';
import 'package:radio_online/utility/hive_utility.dart';

class RadioStationList extends StatefulWidget {
  const RadioStationList({
    required this.data,
    required this.hasMoreData,
    super.key,
  });

  final List<RadioStation> data;
  final bool hasMoreData;

  @override
  State<RadioStationList> createState() => _RadioStationListState();
}

class _RadioStationListState extends State<RadioStationList> {
  int itemCount = 0;

  @override
  void initState() {
    super.initState();
    HiveUtility.addFavoriteListener(() {
      if (mounted) {
        setState(() {});
      }
    });
  }

  Future<void> _refreshScreen() async {
    context.read<LandingScreenCubit>().refreshState();
  }

  @override
  Widget build(BuildContext context) {
    if (widget.data.isEmpty) {
      return Center(
        child: Text(
          AppLocalizations.getTranslatedLabel(context, 'find'),
          style: Theme.of(context).textTheme.titleMedium?.copyWith(
                fontWeight: FontWeight.w600,
              ),
        ),
      );
    }
    itemCount = widget.data.length;
    return NotificationListener<ScrollNotification>(
      onNotification: (notification) {
        if (notification is ScrollEndNotification &&
            notification.metrics.pixels ==
                notification.metrics.maxScrollExtent &&
            widget.hasMoreData) {
          context
              .read<LandingScreenCubit>()
              .loadMoreRadioStations()
              .then((value) {
            context
                .read<PlayerCubit>()
                .onPaginationCallback(stations: widget.data);
          });
        }
        return false;
      },
      child: RefreshIndicator(
        onRefresh: _refreshScreen,
        child: ListView.separated(
          controller: ScrollController(),
          padding: const EdgeInsets.symmetric(vertical: 8),
          physics: const BouncingScrollPhysics(),
          itemCount: itemCount + 1,
          itemBuilder: (context, index) {
            if (index == itemCount) {
              if (widget.hasMoreData) {
                return const Center(
                  child: CircularProgressIndicator(),
                );
              } else {
                return const SizedBox.shrink();
              }
            }
            final isFavorite =
                HiveUtility.isFavoriteRadio(widget.data[index].radioStationId);
            return Card(
              child: InkWell(
                onTap: () {
                  context.read<PlayerCubit>().playRadioPlayList(
                      stationToBePlayed: widget.data[index],
                      allStations: widget.data);
                },
                child: Container(
                  margin: const EdgeInsetsDirectional.symmetric(
                    vertical: 16,
                    horizontal: 4,
                  ),
                  width: MediaQuery.sizeOf(context).width,
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceAround,
                    children: [
                      const SizedBox(width: 8),
                      Container(
                        height: 50,
                        width: 50,
                        decoration: BoxDecoration(
                          color: Theme.of(context).listTileTheme.tileColor,
                        ),
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(50),
                          child: Image.network(
                            fit: BoxFit.fill,
                            widget.data[index].imageUrl,
                            errorBuilder: (context, error, stack) {
                              log('error occurred. loading fallback image');
                              return Image.asset(
                                kFallbackImage,
                              );
                            },
                          ),
                        ),
                      ),
                      Expanded(
                        child: Padding(
                          padding: const EdgeInsetsDirectional.only(start: 16),
                          child: RichText(
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                            text: TextSpan(
                              children: [
                                TextSpan(
                                  text:
                                      '${widget.data[index].radioStationName}\n',
                                  style: Theme.of(context)
                                      .textTheme
                                      .titleMedium
                                      ?.copyWith(
                                        fontWeight: FontWeight.w600,
                                      ),
                                ),
                                TextSpan(
                                  text: widget.data[index].description,
                                  style: Theme.of(context)
                                      .textTheme
                                      .labelLarge
                                      ?.copyWith(
                                        color: Theme.of(context)
                                            .colorScheme
                                            .onSurface
                                            .withOpacity(0.64),
                                      ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                      IconButton(
                        onPressed: () {
                          if (isFavorite) {
                            HiveUtility.deleteFromFavorite(
                              widget.data[index].radioStationId,
                            );
                          } else {
                            HiveUtility.addToFavorite(widget.data[index]);
                          }
                        },
                        icon: Icon(
                          isFavorite
                              ? Icons.favorite_outlined
                              : Icons.favorite_outline,
                          size: 25,
                          color: Theme.of(context).primaryColor,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            );
          },
          separatorBuilder: (BuildContext context, int index) {
            if (index % 5 == 0 && index % 10 != 0) {
              return const AdMobBanner();
            }
            return Container();
          },
        ),
      ),
    );
  }
}
