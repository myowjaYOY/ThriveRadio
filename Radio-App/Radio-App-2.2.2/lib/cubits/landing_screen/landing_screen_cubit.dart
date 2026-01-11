import 'dart:async';
import 'dart:io';

import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:radio_online/cubits/landing_screen/landing_screen_state.dart';
import 'package:radio_online/models/category_model.dart';
import 'package:radio_online/models/city_model.dart';
import 'package:radio_online/models/radio_station.dart';
import 'package:radio_online/models/slider_model.dart';
import 'package:radio_online/repository/radio_station_repo.dart';
import 'package:radio_online/utility/error_message_labels.dart';

class LandingScreenCubit extends Cubit<LandingScreenState> {
  LandingScreenCubit() : super(HomeScreenState(status: Status.initial));

  final RadioStationRepo _repo = RadioStationRepo();
  HomeScreenState? _homeScreenState;
  RadioScreenState? _radioScreenState;
  CategoryScreenState? _categoryScreenState;
  CityScreenState? _cityScreenState;
  LandingScreenState? previousState;

  int radioOffset = 0;
  int categoryOffset = 0;
  int cityOffset = 0;

  Future<void> refreshState() async {
    switch (state) {
      case HomeScreenState():
        _homeScreenState = null;
        await loadHomeScreen();
        return;
      case CategoryScreenState():
        _categoryScreenState = null;
        await loadCategoryScreen();
        return;
      case CityScreenState():
        _cityScreenState = null;
        await loadCityScreen();
        return;
      case RadioScreenState():
        _radioScreenState = null;
        await loadRadioScreen();
        return;
    }
  }

  ///This case is for radio screen only. Other screens are normally navigated through
  ///nav bar and drawer in which case it will navigate to home screen on back
  ///
  ///Loading previous state when the user presses back button.
  ///Case: when user navigates to radio screen through city, category or search
  void loadPreviousState() {
    if (previousState != null) {
      ///If the user navigated through city or category screen
      emit(previousState!);
      previousState = null;
    } else {
      ///Not the best approach. But works :)
      if ((state as RadioScreenState).filterName == null) {
        ///Load home screen if the state is default radio screen
        loadHomeScreen();
      } else {
        ///Load radio screen if the state is searched state
        loadRadioScreen();
      }
      previousState = null;
    }
  }

  ///Home Screen - fetches from AzuraCast
  Future<void> loadHomeScreen() async {
    if (_homeScreenState != null) {
      emit(_homeScreenState!);
      return;
    }
    
    try {
      emit(HomeScreenState(status: Status.loading));
      final data = await Future.wait([
        _repo.getSliders(),
        _repo.getRadioStations(limit: 10),
        _repo.getCategories(limit: 10),
      ]);
      if (!isClosed) {
        emit(
          HomeScreenState(
            status: Status.loaded,
            sliders: data[0] as List<SliderModel>,
            latestList: data[1] as List<RadioStation>,
            categoryList: data[2] as List<CategoryModel>,
          ),
        );
        _homeScreenState = state as HomeScreenState;
      }
    } on SocketException {
      // No internet - show error
      emit(
        HomeScreenState(
          status: Status.error,
          errorMessage: noInternet,
        ),
      );
    } on Exception catch (e) {
      // API error - show error with message
      emit(
        HomeScreenState(
          status: Status.error,
          errorMessage: e.toString(),
        ),
      );
    }
  }

  ///Radio Screen Functions
  Future<void> loadRadioScreen() async {
    if (_radioScreenState != null) {
      emit(_radioScreenState!);
      return;
    }
    radioOffset = 0;
    try {
      emit(RadioScreenState(status: Status.loading));
      final radioList = await _repo.getRadioStations(limit: 10);
      emit(
        RadioScreenState(
          status: Status.loaded,
          radioList: radioList,
          hasMoreData: radioList.length == 10,

          ///This needs to be true initially else new data won't load dynamically if it exists
        ),
      );
      _radioScreenState = state as RadioScreenState;
    } on Exception catch (e) {
      emit(RadioScreenState(status: Status.error, errorMessage: e.toString()));
    }
  }

  Future<void> loadMoreRadioStations() async {
    radioOffset += 10;
    try {
      final newList =
          await _repo.getRadioStations(limit: 10, offset: '$radioOffset');
      final updatedList = [
        ...List<RadioStation>.from((state as RadioScreenState).radioList!),
        ...newList,
      ];
      emit(
        RadioScreenState(
          status: Status.loaded,
          radioList: updatedList,
          hasMoreData: newList.length == 10,
        ),
      );
      _radioScreenState = state as RadioScreenState;
    } on Exception catch (e) {
      emit(RadioScreenState(status: Status.error, errorMessage: e.toString()));
    }
  }

  Future<void> loadRadioScreenByCity({
    required int cityId,
    required String cityName,
  }) async {
    previousState = state;
    try {
      emit(RadioScreenState(status: Status.loading));
      final radioList = await _repo.getRadioStationsByCity(cityId);
      emit(
        RadioScreenState(
          status: Status.loaded,
          radioList: radioList,
          // filterName: cityName,
        ),
      );
    } on Exception catch (e) {
      emit(RadioScreenState(status: Status.error, errorMessage: e.toString()));
    }
  }

  Future<void> loadRadioScreenByCategory({
    required int categoryId,
    required String categoryName,
  }) async {
    previousState = state;
    try {
      emit(RadioScreenState(status: Status.loading));
      final radioList = await _repo.getRadioStationsByCategory(categoryId);
      emit(
        RadioScreenState(
          status: Status.loaded,
          radioList: radioList,
          // filterName: categoryName,
        ),
      );
    } on Exception catch (e) {
      emit(RadioScreenState(status: Status.error, errorMessage: e.toString()));
    }
  }

  Future<void> loadRadioScreenByQuery({required String query}) async {
    try {
      emit(RadioScreenState(status: Status.loading));
      final radioList = await _repo.getRadioStationsByQuery(query);
      emit(
        RadioScreenState(
          status: Status.loaded,
          radioList: radioList,
          filterName: query,
        ),
      );
    } on Exception catch (e) {
      emit(RadioScreenState(status: Status.error, errorMessage: e.toString()));
    }
  }

  ///City Screen Functions
  Future<void> loadCityScreen() async {
    if (_cityScreenState != null) {
      emit(_cityScreenState!);
      return;
    }
    cityOffset = 0;
    try {
      emit(CityScreenState(status: Status.loading));
      final cityList = await _repo.getCities(limit: 10);
      emit(
        CityScreenState(
          status: Status.loaded,
          cityList: cityList,
          hasMoreData: cityList.length == 10,
        ),
      );
      _cityScreenState = state as CityScreenState;
    } on Exception catch (e) {
      emit(CityScreenState(status: Status.error, errorMessage: e.toString()));
    }
  }

  Future<void> loadMoreCities() async {
    cityOffset += 10;
    try {
      final newList = await _repo.getCities(limit: 10, offset: cityOffset);
      final updatedList = [
        ...List<CityModel>.from((state as CityScreenState).cityList!),
        ...newList,
      ];
      emit(
        CityScreenState(
          status: Status.loaded,
          cityList: updatedList,
          hasMoreData: newList.length == 10,
        ),
      );
      _cityScreenState = state as CityScreenState;
    } on Exception catch (e) {
      emit(CityScreenState(status: Status.error, errorMessage: e.toString()));
    }
  }

  ///Category Screen Functions
  Future<void> loadCategoryScreen() async {
    if (_categoryScreenState != null) {
      emit(_categoryScreenState!);
      return;
    }
    categoryOffset = 0;
    try {
      emit(CategoryScreenState(status: Status.loading));
      final categoryList = await _repo.getCategories(limit: 10);
      emit(
        CategoryScreenState(
          status: Status.loaded,
          categoryList: categoryList,
          hasMoreData: categoryList.length == 10,
        ),
      );
      _categoryScreenState = state as CategoryScreenState;
    } on Exception catch (e) {
      emit(
        CategoryScreenState(
          status: Status.error,
          errorMessage: e.toString(),
        ),
      );
    }
  }

  Future<void> loadMoreCategories() async {
    categoryOffset += 10;
    try {
      final newList =
          await _repo.getCategories(limit: 10, offset: categoryOffset);
      final updatedList = [
        ...List<CategoryModel>.from(
          (state as CategoryScreenState).categoryList!,
        ),
        ...newList,
      ];
      emit(
        CategoryScreenState(
          status: Status.loaded,
          categoryList: updatedList,
          hasMoreData: newList.length == 10,
        ),
      );
      _categoryScreenState = state as CategoryScreenState;
    } on Exception catch (e) {
      emit(
        CategoryScreenState(
          status: Status.error,
          errorMessage: e.toString(),
        ),
      );
    }
  }
}
