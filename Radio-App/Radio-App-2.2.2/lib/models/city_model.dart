class CityModel {
  const CityModel({required this.cityId, required this.cityName});

  CityModel.fromJson(Map<String, dynamic> json)
      : cityId = json['id'] as int,
        cityName = json['name'] as String;

  final int cityId;
  final String cityName;
}
