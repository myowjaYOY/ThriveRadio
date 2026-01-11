import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:radio_online/cubits/landing_screen/landing_screen_cubit.dart';
import 'package:radio_online/cubits/player/player_cubit.dart';
import 'package:radio_online/models/category_model.dart';
import 'package:radio_online/models/radio_station.dart';
import 'package:radio_online/utility/constants.dart';

class HorizontalListView extends StatelessWidget {
  const HorizontalListView({required this.data, super.key});

  final List<dynamic> data;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: MediaQuery.sizeOf(context).width,
      height: 120,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        physics: const BouncingScrollPhysics(),
        itemCount: data.length,
        itemBuilder: (context, index) {
          return GestureDetector(
            onTap: () {
              if (data is List<RadioStation>) {
                context.read<PlayerCubit>().playRadioPlayList(
                    stationToBePlayed: data[index],
                    allStations: data as List<RadioStation>);
              } else {
                context.read<LandingScreenCubit>().loadRadioScreenByCategory(
                      categoryId: (data[index] as CategoryModel).categoryId,
                      categoryName: (data[index] as CategoryModel).categoryName,
                    );
              }
            },
            child: Column(
              mainAxisSize: MainAxisSize.min,
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: [
                Container(
                  height: 90,
                  width: 90,
                  margin: const EdgeInsetsDirectional.symmetric(horizontal: 10),
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(100),
                    child: Image.network(
                      fit: BoxFit.fill,
                      (data is List<RadioStation>)
                          ? (data[index] as RadioStation).imageUrl
                          : (data[index] as CategoryModel).imageUrl,
                      errorBuilder: (context, error, stack) {
                        return Image.asset(fit: BoxFit.fill, kFallbackImage);
                      },
                    ),
                  ),
                ),
                SizedBox(
                  width: 100,
                  child: Center(
                    child: Text(
                      (data is List<RadioStation>)
                          ? (data[index] as RadioStation).radioStationName
                          : (data[index] as CategoryModel).categoryName,
                      overflow: TextOverflow.ellipsis,
                      style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                            fontWeight: FontWeight.w600,
                          ),
                    ),
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}
