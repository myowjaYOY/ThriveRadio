import 'package:radio_online/models/category_model.dart';
import 'package:radio_online/models/city_model.dart';
import 'package:radio_online/models/radio_station.dart';
import 'package:radio_online/models/slider_model.dart';
import 'package:radio_online/utility/app_info.dart';

enum Status { initial, loading, loaded, error }

abstract class LandingScreenState {
  abstract final int id;
}

class HomeScreenState extends LandingScreenState {
  HomeScreenState({
    required this.status,
    this.sliders,
    this.categoryList,
    this.latestList,
    this.errorMessage,
  });

  @override
  final int id = 0;

  final Status status;
  final List<SliderModel>? sliders;
  final List<CategoryModel>? categoryList;
  final List<RadioStation>? latestList;
  final String? errorMessage;
}

class CityScreenState extends LandingScreenState {
  CityScreenState({
    required this.status,
    this.cityList,
    this.errorMessage,
    this.hasMoreData,
  });

  @override
  final int id = 2;

  final List<CityModel>? cityList;
  final Status status;
  final String? errorMessage;
  final bool? hasMoreData;
}

class CategoryScreenState extends LandingScreenState {
  CategoryScreenState({
    required this.status,
    this.categoryList,
    this.errorMessage,
    this.hasMoreData,
  });

  @override
  final int id = 1;

  final List<CategoryModel>? categoryList;
  final Status status;
  final String? errorMessage;
  final bool? hasMoreData;
}

class RadioScreenState extends LandingScreenState {
  RadioScreenState({
    required this.status,
    this.radioList,
    this.errorMessage,
    this.filterName,
    this.hasMoreData,
  });

  @override
  final int id = (AppInfo().cityMode) ? 3 : 2;

  final Status status;
  final List<RadioStation>? radioList;
  final String? errorMessage;
  final String? filterName;
  final bool? hasMoreData;

  ///handles lazy loading feature. Needs a better solution.
}
