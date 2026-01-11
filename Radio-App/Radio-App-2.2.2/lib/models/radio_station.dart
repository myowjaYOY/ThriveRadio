import 'package:hive/hive.dart';

part 'radio_station.g.dart';

@HiveType(typeId: 1)
class RadioStation extends HiveObject {
  RadioStation({
    required this.radioStationId,
    required this.radioStationName,
    required this.imageUrl,
    required this.radioUrl,
    this.description,
  });

  /// Parse from PHP Admin Panel format (legacy)
  RadioStation.fromJson(Map<String, dynamic> json)
      : radioStationId = json['id'] as int,
        radioStationName = json['name'] as String,
        imageUrl = json['image'] as String,
        radioUrl = json['radio_url'] as String,
        description = json['description'] as String?;

  /// Parse from AzuraCast nowplaying API response
  factory RadioStation.fromAzuraCast(Map<String, dynamic> json) {
    final station = json['station'] as Map<String, dynamic>;
    final nowPlaying = json['now_playing'] as Map<String, dynamic>?;
    final song = nowPlaying?['song'] as Map<String, dynamic>?;
    
    // Get artwork from now playing song, or use a default
    String artUrl = song?['art'] as String? ?? '';
    if (artUrl.isEmpty) {
      artUrl = 'https://azuracast-radio-u62352.vm.elestio.app/static/img/generic_song.jpg';
    }
    
    return RadioStation(
      radioStationId: station['id'] as int,
      radioStationName: station['name'] as String,
      imageUrl: artUrl,
      radioUrl: station['listen_url'] as String,
      description: station['description'] as String?,
    );
  }

  /// Parse from AzuraCast stations API response (without nowplaying)
  factory RadioStation.fromAzuraCastStation(Map<String, dynamic> json) {
    return RadioStation(
      radioStationId: json['id'] as int,
      radioStationName: json['name'] as String,
      imageUrl: 'https://azuracast-radio-u62352.vm.elestio.app/static/img/generic_song.jpg',
      radioUrl: json['listen_url'] as String,
      description: json['description'] as String?,
    );
  }

  @HiveField(0)
  final int radioStationId;
  @HiveField(1)
  final String radioStationName;
  @HiveField(2)
  final String imageUrl;
  @HiveField(3)
  final String radioUrl;
  @HiveField(4)
  final String? description;

  @override
  operator ==(Object other) =>
      identical(this, other) ||
      other is RadioStation && radioStationId == other.radioStationId;

  @override
  int get hashCode => radioStationId.hashCode;
}
