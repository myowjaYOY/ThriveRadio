import 'dart:developer';

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:radio_online/models/radio_station.dart';
import 'package:radio_online/utility/app_localization.dart';
import 'package:radio_online/utility/constants.dart';
import 'package:radio_online/utility/hive_utility.dart';

class FavoriteScreen extends StatefulWidget {
  const FavoriteScreen({required this.onTap, super.key});

  final void Function(int index, List<RadioStation> favoriteList) onTap;

  @override
  State<FavoriteScreen> createState() => _FavoriteScreenState();
}

class _FavoriteScreenState extends State<FavoriteScreen> {
  late final List<RadioStation> favoriteList;

  @override
  void initState() {
    super.initState();
    favoriteList = HiveUtility.favoriteList;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: Theme.of(context).primaryColor,
        foregroundColor: Theme.of(context).colorScheme.onTertiary,
        centerTitle: true,
        title: Text(AppLocalizations.getTranslatedLabel(context, 'favourite')),
        leading: IconButton(
          onPressed: Navigator.of(context).pop,
          icon: Icon(
            CupertinoIcons.left_chevron,
            color: Theme.of(context).colorScheme.onTertiary,
          ),
        ),
      ),
      body: favoriteList.isEmpty
          ? Center(
              child: Text(
                  AppLocalizations.getTranslatedLabel(context, 'fav_radio')),
            )
          : Padding(
              padding: const EdgeInsetsDirectional.all(8),
              child: ListView.builder(
                controller: ScrollController(),
                physics: const BouncingScrollPhysics(),
                itemCount: favoriteList.length,
                itemExtent: 100,
                itemBuilder: (context, index) {
                  return Card(
                    child: InkWell(
                      onTap: () {
                        widget.onTap(index, favoriteList);
                        Navigator.of(context).pop();
                      },
                      child: Container(
                        margin: const EdgeInsetsDirectional.symmetric(
                          vertical: 8,
                          horizontal: 4,
                        ),
                        width: MediaQuery.sizeOf(context).width,
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceAround,
                          children: [
                            Container(
                              height: 50,
                              width: 50,
                              decoration: BoxDecoration(
                                color:
                                    Theme.of(context).listTileTheme.tileColor,
                              ),
                              child: ClipRRect(
                                borderRadius: BorderRadius.circular(50),
                                child: Image.network(
                                  fit: BoxFit.fill,
                                  favoriteList[index].imageUrl,
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
                                padding:
                                    const EdgeInsetsDirectional.only(start: 16),
                                child: RichText(
                                  maxLines: 2,
                                  overflow: TextOverflow.ellipsis,
                                  text: TextSpan(
                                    children: [
                                      TextSpan(
                                        text:
                                            '${favoriteList[index].radioStationName}\n',
                                        style: Theme.of(context)
                                            .textTheme
                                            .titleMedium
                                            ?.copyWith(
                                              fontWeight: FontWeight.w600,
                                            ),
                                      ),
                                      TextSpan(
                                        text: favoriteList[index].description,
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
                                HiveUtility.deleteFromFavorite(
                                  favoriteList[index].radioStationId,
                                );
                                favoriteList.remove(favoriteList[index]);
                                setState(() {});
                              },
                              icon: Icon(
                                Icons.favorite_outlined,
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
              ),
            ),
    );
  }
}
