import 'dart:math' as math;

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:radio_online/cubits/player/player_cubit.dart';
import 'package:radio_online/models/radio_station.dart';
import 'package:radio_online/ui/screens/favorite_screen.dart';
import 'package:radio_online/utility/app_localization.dart';
import 'package:radio_online/utility/constants.dart';
import 'package:radio_online/utility/hive_utility.dart';

class FavoritesGrid extends StatefulWidget {
  const FavoritesGrid({super.key});

  @override
  State<FavoritesGrid> createState() => _FavoritesGridState();
}

class _FavoritesGridState extends State<FavoritesGrid> {
  late List<RadioStation> _favoriteList;

  @override
  void initState() {
    super.initState();
    HiveUtility.addFavoriteListener(() {
      if (mounted) {
        setState(() {});
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    _favoriteList = HiveUtility.favoriteList;
    final int count = math.min(_favoriteList.length, 4);
    if (_favoriteList.isEmpty) return const SizedBox.shrink();
    final Size size = MediaQuery.sizeOf(context);

    return Padding(
      padding: const EdgeInsetsDirectional.fromSTEB(12, 12, 12, 8),
      child: SizedBox(
        width: size.width,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Row(
              children: [
                Expanded(
                  child: Text(
                      AppLocalizations.getTranslatedLabel(context, 'favourite'),
                      style: Theme.of(context).textTheme.titleLarge?.copyWith(
                            fontWeight: FontWeight.w600,
                          )),
                ),
                IconButton(
                  onPressed: () => Navigator.of(context).push(
                    MaterialPageRoute<dynamic>(
                      builder: (_) => FavoriteScreen(
                        onTap: (index, allStations) {
                          context.read<PlayerCubit>().playRadioPlayList(
                              stationToBePlayed: allStations[index],
                              allStations: allStations);
                        },
                      ),
                    ),
                  ),
                  icon: const Icon(
                    Icons.chevron_right,
                  ),
                ),
              ],
            ),
            AnimatedContainer(
              duration: const Duration(milliseconds: 500),
              width: size.width,
              height: math.max(190, (count / 2).ceil() * 190),
              child: GridView.count(
                crossAxisCount: 2,
                crossAxisSpacing: 12,
                physics: const NeverScrollableScrollPhysics(),
                children: List.generate(count, (index) {
                  return GestureDetector(
                    onTap: () => context.read<PlayerCubit>().playRadioPlayList(
                        stationToBePlayed: _favoriteList[index],
                        allStations: _favoriteList),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                      children: [
                        Expanded(
                          flex: 3,
                          child: ClipRRect(
                            borderRadius: BorderRadius.circular(16),
                            child: SizedBox.expand(
                              child: Image.network(
                                _favoriteList[index].imageUrl,
                                errorBuilder: (context, err, stack) {
                                  return Image.asset(kFallbackImage);
                                },
                                fit: BoxFit.cover,
                              ),
                            ),
                          ),
                        ),
                        Flexible(
                          child: Text(
                            _favoriteList[index].radioStationName,
                            style: Theme.of(context)
                                .textTheme
                                .bodyMedium
                                ?.copyWith(
                                  fontWeight: FontWeight.w600,
                                ),
                          ),
                        ),
                      ],
                    ),
                  );
                }),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
