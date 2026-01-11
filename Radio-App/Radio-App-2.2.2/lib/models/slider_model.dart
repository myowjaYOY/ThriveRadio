class SliderModel {
  const SliderModel({
    required this.sliderId,
    required this.sliderTitle,
    required this.imageUrl,
    required this.radioStationId,
  });

  SliderModel.fromJson(Map<String, dynamic> json)
      : sliderId = json['id'] as int,
        sliderTitle = json['title'] as String,
        imageUrl = json['image'] as String,
        radioStationId = json['radio_station_id'] as int;

  final int sliderId;
  final String sliderTitle;
  final String imageUrl;
  final int radioStationId;
}
