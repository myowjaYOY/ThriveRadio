import 'dart:async';
import 'dart:math';
import 'package:audio_service/audio_service.dart';
import 'package:audio_session/audio_session.dart';
import 'package:flutter/services.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:just_audio/just_audio.dart';
import 'package:radio_online/auth/services/analytics_service.dart';
import 'package:radio_online/cubits/player/player_cubit_state.dart';
import 'package:radio_online/models/radio_station.dart';
import 'package:radio_online/repository/radio_station_repo.dart';
import 'package:connectivity_plus/connectivity_plus.dart';

class PlayerCubit extends Cubit<PlayerCubitState> {
  // Singleton instance to prevent duplicate AudioPlayer creation
  static AudioPlayer? _sharedAudioPlayer;
  static bool _isDisposing = false;
  static const int _maxRetries = 3;

  PlayerCubit() : super(InitialState()) {
    _initializeConnectivityListener();
    _checkInitialConnectivity();
    _initializeAudioSession();
  }

  late AudioPlayer _audioPlayer;
  bool _isAudioPlayerInitialized = false;
  bool isConnectedCheck = true;

  bool isInitialPlayListLoaded = false;

  List<RadioStation> radioStationPlayList = [];
  RadioStation? currentlyPlayingStation;
  ConcatenatingAudioSource? playlist;

  StreamSubscription<PlayerState>? _playerStateSubscription;
  StreamSubscription<SequenceState?>? _sequenceStateSubscription;
  StreamSubscription<List<ConnectivityResult>>? _connectivitySubscription;
  AudioSession? _audioSession;
  Timer? _retryTimer;
  int _retryCount = 0;
  bool _isRetrying = false;

  // Analytics service for tracking listen events
  final AnalyticsService _analytics = AnalyticsService.instance;

  /// Track station change for analytics (ends previous session, starts new one)
  Future<void> _trackStationStart(RadioStation station) async {
    await _analytics.trackStationStart(
      stationId: station.radioStationId,
      stationName: station.radioStationName,
    );
  }

  // This ensures we don't create multiple AudioPlayer instances
  static Future<AudioPlayer> _getOrCreateAudioPlayer() async {
    if (_isDisposing) {
      // Wait for disposal to complete before creating a new instance
      await Future.delayed(Duration(milliseconds: 500));
      _isDisposing = false;
    }

    if (_sharedAudioPlayer != null) {
      try {
        // Check if the player is still usable
        await _sharedAudioPlayer!.pause();
        return _sharedAudioPlayer!;
      } catch (e) {
        print('Existing player unusable, creating new one: $e');
        // If we get an exception, the player is likely disposed or broken
        await _disposeSharedPlayer();
      }
    }

    // Create a new player
    _sharedAudioPlayer = AudioPlayer(
      handleInterruptions: true,
      androidApplyAudioAttributes: true,
      handleAudioSessionActivation: true,
      // Reduced buffer for live streaming (default is 50 seconds)
      audioLoadConfiguration: const AudioLoadConfiguration(
        androidLoadControl: AndroidLoadControl(
          minBufferDuration: Duration(seconds: 5),
          maxBufferDuration: Duration(seconds: 10),
          bufferForPlaybackDuration: Duration(seconds: 2),
          bufferForPlaybackAfterRebufferDuration: Duration(seconds: 3),
        ),
        darwinLoadControl: DarwinLoadControl(
          preferredForwardBufferDuration: Duration(seconds: 5),
        ),
      ),
    );

    return _sharedAudioPlayer!;
  }

  // Safely dispose the shared player
  static Future<void> _disposeSharedPlayer() async {
    if (_sharedAudioPlayer != null && !_isDisposing) {
      _isDisposing = true;
      try {
        await _sharedAudioPlayer!.stop();
        await _sharedAudioPlayer!.dispose();
      } catch (e) {
        print('Error disposing shared player: $e');
      } finally {
        _sharedAudioPlayer = null;
        _isDisposing = false;
      }
    }
  }

  void _initializeConnectivityListener() {
    _connectivitySubscription = Connectivity()
        .onConnectivityChanged
        .listen((List<ConnectivityResult> result) {
      if (result.contains(ConnectivityResult.none)) {
        isConnectedCheck = false;
        pauseRadio();
      } else {
        isConnectedCheck = true;
        resumeRadio();
      }
    });
  }

  void _checkInitialConnectivity() async {
    var connectivityResult = await (Connectivity().checkConnectivity());
    if (connectivityResult.contains(ConnectivityResult.none)) {
      isConnectedCheck = false;
    } else {
      isConnectedCheck = true;
    }
  }

  // Reinitialize player if it's in a bad state
  Future<void> _reinitializePlayer() async {
    print('Reinitializing audio player');
    try {
      // Cancel any existing subscriptions
      _playerStateSubscription?.cancel();
      _sequenceStateSubscription?.cancel();

      // Dispose of old player
      await _disposeSharedPlayer();

      // Create a new player
      _audioPlayer = await _getOrCreateAudioPlayer();
      _isAudioPlayerInitialized = true;

      // Set up listeners
      _listenPlayerStateStream();
      _listenSequenceStateStream();

      // If we had a playlist, try to restore it
      if (playlist != null && currentlyPlayingStation != null) {
        try {
          int currentIndex = getCurrentlyPlayingStationIndex();
          if (currentIndex >= 0) {
            await _audioPlayer.setAudioSource(playlist!,
                initialIndex: currentIndex,
                initialPosition: Duration.zero,
                preload: false);
          }
        } catch (e) {
          print('Error restoring playlist: $e');
        }
      }
    } catch (e) {
      print('Error reinitializing player: $e');
    }
  }

  // Initialize audio session and player
  void _initializeAudioSession() async {
    try {
      print('Audio initializing');
      _audioSession = await AudioSession.instance;
      await _audioSession?.configure(AudioSessionConfiguration.music());

      // Use the singleton method to get/create the AudioPlayer
      _audioPlayer = await _getOrCreateAudioPlayer();
      _isAudioPlayerInitialized = true;

      _handleInterruptions(_audioSession!);
      _monitorPlayerErrors();
    } catch (e) {
      print('Error initializing audio: $e');
      emit(FailureState());
    }
  }

  // Set up error monitoring for the player
  void _monitorPlayerErrors() {
    _audioPlayer.playbackEventStream.listen((event) {},
        onError: (Object e, StackTrace st) {
      print('Error from playback event stream: $e');
      if (e is PlatformException) {
        _handlePlaybackError();
      }
    });

    _audioPlayer.processingStateStream.listen((state) {
      if (state == ProcessingState.idle && currentlyPlayingStation != null) {
        // If we unexpectedly go to idle while a station should be playing, try to recover
        _handlePlaybackError();
      }
    });
  }

  // Handle playback errors with automatic retry
  void _handlePlaybackError() {
    // End current listen session on playback error
    unawaited(_analytics.endCurrentListenSession());
    
    if (_isRetrying) return;
    _isRetrying = true;

    print('Handling playback error, retry count: $_retryCount');

    if (_retryCount < _maxRetries) {
      _retryCount++;

      // Cancel any previous retry timer
      _retryTimer?.cancel();

      // Schedule a retry after a delay
      _retryTimer = Timer(Duration(seconds: 2), () async {
        try {
          emit(LoadingState());
          await _reinitializePlayer();
          if (currentlyPlayingStation != null) {
            await resumeRadioWithRetry();
          }
        } finally {
          _isRetrying = false;
        }
      });
    } else {
      // If we've tried too many times, reset and show failure
      _retryCount = 0;
      _isRetrying = false;
      emit(FailureState());
    }
  }

  // Resume with retry logic to handle edge cases
  Future<void> resumeRadioWithRetry() async {
    if (currentlyPlayingStation == null || !isConnectedCheck) return;

    try {
      if (_audioPlayer.processingState == ProcessingState.idle) {
        // We need to set the source again
        if (playlist != null) {
          int index = getCurrentlyPlayingStationIndex();
          if (index >= 0) {
            await _audioPlayer.setAudioSource(playlist!,
                initialIndex: index,
                initialPosition: Duration.zero,
                preload: false);
          }
        } else {
          // Recreate a single-item playlist
          await _audioPlayer.setAudioSource(
              AudioSource.uri(
                Uri.parse(currentlyPlayingStation!.radioUrl),
                tag: MediaItem(
                  id: currentlyPlayingStation!.radioStationId.toString(),
                  title: currentlyPlayingStation!.radioStationName,
                  artUri: Uri.parse(currentlyPlayingStation!.imageUrl),
                  artist: currentlyPlayingStation!.description,
                ),
              ),
              preload: false);
        }
      }

      await _audioPlayer.play();
      emit(PlayingState());
      // Reset retry count on success
      _retryCount = 0;
    } catch (e) {
      print('Error in resumeRadioWithRetry: $e');
      _handlePlaybackError();
    }
  }

  void _handleInterruptions(AudioSession audioSession) {
    bool playInterrupted = false;

    // Consolidate the becomingNoisy listeners into one
    audioSession.becomingNoisyEventStream.listen((_) {
      if (_audioPlayer.playing) {
        _audioPlayer.pause();
      }
    });

    _audioPlayer.playingStream.listen((playing) {
      print('DEBUG: playingStream emitted: $playing');
      playInterrupted = false;
      if (playing) {
        try {
          print('DEBUG: Setting audio session active');
          audioSession.setActive(true);
        } catch (e) {
          print('Error setting audio session active: $e');
        }
      }
    });

    audioSession.interruptionEventStream.listen((event) {
      if (event.begin) {
        switch (event.type) {
          case AudioInterruptionType.duck:
            if (audioSession.androidAudioAttributes?.usage ==
                AndroidAudioUsage.game) {
              _audioPlayer.setVolume(_audioPlayer.volume / 2);
            }
            playInterrupted = false;
            break;
          case AudioInterruptionType.pause:
          case AudioInterruptionType.unknown:
            if (_audioPlayer.playing) {
              _audioPlayer.pause();
              playInterrupted = true;
            }
            break;
        }
      } else {
        switch (event.type) {
          case AudioInterruptionType.duck:
            _audioPlayer.setVolume(min(1.0, _audioPlayer.volume * 2));
            playInterrupted = false;
            break;
          case AudioInterruptionType.pause:
            if (playInterrupted) {
              _audioPlayer.play().catchError((error) {
                print('Error resuming playback: $error');
                _handlePlaybackError();
              });
            }
            playInterrupted = false;
            break;
          case AudioInterruptionType.unknown:
            playInterrupted = false;
            break;
        }
      }
    });
  }

  // Function to add pagination items to playlist and will be passed to screens
  void onPaginationCallback({required List<RadioStation> stations}) async {
    if (radioStationPlayList.isEmpty) {
      return;
    }
    bool hasAllStations = false;
    for (int i = 0; i < stations.length; i++) {
      if (radioStationPlayList.contains(stations[i]) &&
          radioStationPlayList.indexOf(stations[i]) == i) {
        hasAllStations = true;
      } else {
        hasAllStations = false;
        break;
      }
    }
    if (hasAllStations) {
      for (int i = 0; i < stations.length; i++) {
        if (!radioStationPlayList.contains(stations[i])) {
          radioStationPlayList.add(stations[i]);
          try {
            await playlist?.add(getAudioSourceFromStation(stations[i]));
          } catch (e) {
            print('Error adding to playlist: $e');
          }
        }
      }
      emit(state);
    }
  }

  int getCurrentlyPlayingStationIndex() {
    return radioStationPlayList
        .indexWhere((element) => element == currentlyPlayingStation);
  }

  bool hasPreviousStation() {
    return getCurrentlyPlayingStationIndex() > 0 &&
        currentlyPlayingStation != null &&
        radioStationPlayList.length > 1;
  }

  bool hasNextStation() {
    return getCurrentlyPlayingStationIndex() <
            radioStationPlayList.length - 1 &&
        currentlyPlayingStation != null;
  }

  AudioSource getAudioSourceFromStation(RadioStation station) {
    return AudioSource.uri(
      Uri.parse(station.radioUrl),
      tag: MediaItem(
        id: station.radioStationId.toString(),
        title: station.radioStationName,
        artUri: Uri.parse(station.imageUrl),
        artist: station.description,
      ),
    );
  }

  // Handles logic for playing radio station from any screen
  Future<void> playRadioPlayList(
      {required RadioStation stationToBePlayed,
      required List<RadioStation> allStations}) async {
    // Reset retry counter when user explicitly selects a station
    _retryCount = 0;

    print('Tapped 1');
    // If user taps on same station, do nothing
    if (currentlyPlayingStation == stationToBePlayed) {
      print('Tapped 2');

      // Resume playback if paused
      if (!_audioPlayer.playing) {
        print('Tapped 2-1');

        try {
          await pauseRadio(); //Temp
          await Future.delayed(Duration(seconds: 1));
          await resumeRadioWithRetry();
        } catch (e) {
          print('Error playing same station: $e');
          emit(FailureState());
        }
      }
      return;
    } else {
      print('Tapped 3');

      emit(LoadingState());
      try {
        // If there's currently a playlist check if it's the same as the one we're trying to play
        print('tapped 3-$isInitialPlayListLoaded');
        if (radioStationPlayList.isNotEmpty && isInitialPlayListLoaded) {
          print('Tapped 3-1');
          bool hasAllStations = true;
          for (int i = 0; i < allStations.length; i++) {
            if (!radioStationPlayList.contains(allStations[i]) ||
                radioStationPlayList.indexOf(allStations[i]) != i) {
              hasAllStations = false;
              break;
            }
          }
          if (hasAllStations) {
            // All stations are same, so play the song from the playlist
            // But if there are more stations in playlist, add those to our playlist
            if (allStations.length != radioStationPlayList.length) {
              for (int i = radioStationPlayList.length;
                  i < allStations.length;
                  i++) {
                radioStationPlayList.add(allStations[i]);
                try {
                  await playlist
                      ?.add(getAudioSourceFromStation(allStations[i]));
                } catch (e) {
                  print('Error adding to playlist: $e');
                }
              }
            }

            try {
              // Stop before seeking to prevent conflicts
              print('Tapped 3-2');
              if (_audioPlayer.playing) {
                await _audioPlayer.stop();
              }

              await _audioPlayer.seek(null,
                  index: radioStationPlayList.indexOf(stationToBePlayed));
              await _audioPlayer.load();
              await _audioPlayer.play();

              currentlyPlayingStation = stationToBePlayed;
              unawaited(_trackStationStart(stationToBePlayed)); // Analytics
              emit(PlayingState());
            } catch (e) {
              print('Error seeking to station: $e');
              _handlePlaybackError();
            }
            return;
          }
        }

        // Below is the logic for playing a new radio play list
        // Clean up existing player resources before setting new source
        // if (_audioPlayer.playing) {
        //   await _audioPlayer.stop();
        // }

        // Clear previous listeners
        _playerStateSubscription?.cancel();
        _sequenceStateSubscription?.cancel();

        radioStationPlayList.clear();
        radioStationPlayList.addAll(allStations);
        currentlyPlayingStation = stationToBePlayed;

        playlist = ConcatenatingAudioSource(
          children: allStations.map((station) {
            return getAudioSourceFromStation(station);
          }).toList(),
        );

        isInitialPlayListLoaded = false;

        try {
          // Check if we need a fresh player
          if (_audioPlayer.processingState == ProcessingState.idle ||
              _audioPlayer.processingState == ProcessingState.completed) {
            await _reinitializePlayer();
          }

          // Set new audio source
          await _audioPlayer.setAudioSource(playlist!,
              preload: false,
              initialIndex: getCurrentlyPlayingStationIndex(),
              initialPosition: Duration.zero);

          await _audioPlayer.play();
          isInitialPlayListLoaded = true;

          // Set up listeners
          _listenPlayerStateStream();
          _listenSequenceStateStream();

          unawaited(_trackStationStart(stationToBePlayed)); // Analytics
          emit(PlayingState());
        } catch (e) {
          print('Error setting audio source: $e');
          _handlePlaybackError();
        }
      } catch (e) {
        print('Error in playRadioPlayList: $e');
        _handlePlaybackError();
      }
    }
  }

  Future<void> playRadioFromId(int id) async {
    try {
      emit(LoadingState());
      final RadioStation? station =
          await RadioStationRepo().getRadioStationById(id);
      if (station != null) {
        await playRadioPlayList(
            stationToBePlayed: station, allStations: [station]);
      } else {
        emit(FailureState());
      }
    } catch (e) {
      print('Error playing radio from ID: $e');
      emit(FailureState());
    }
  }

  void playNext() async {
    if (radioStationPlayList.isEmpty || currentlyPlayingStation == null) {
      return;
    }

    emit(LoadingState());
    try {
      if (isInitialPlayListLoaded) {
        int nextIndex = getCurrentlyPlayingStationIndex() + 1;
        if (nextIndex < radioStationPlayList.length) {
          // Safety check before changing
          if (_audioPlayer.playing) {
            await _audioPlayer.stop();
          }

          // Check if player is in a good state
          if (_audioPlayer.processingState == ProcessingState.idle ||
              _audioPlayer.processingState == ProcessingState.completed) {
            await _reinitializePlayer();

            // Seek directly to the next index
            await _audioPlayer.setAudioSource(playlist!,
                initialIndex: nextIndex,
                initialPosition: Duration.zero,
                preload: false);
          } else {
            await _audioPlayer.seek(null, index: nextIndex);
          }

          await _audioPlayer.play();

          currentlyPlayingStation = radioStationPlayList[nextIndex];
          unawaited(_trackStationStart(currentlyPlayingStation!)); // Analytics
          emit(PlayingState());
        }
      } else {
        List<RadioStation> tempList = List.from(radioStationPlayList);
        int nextIndex = getCurrentlyPlayingStationIndex() + 1;

        if (nextIndex < radioStationPlayList.length) {
          playRadioPlayList(
              stationToBePlayed: radioStationPlayList[nextIndex],
              allStations: tempList);
        }
      }
    } catch (e) {
      print('Error playing next: $e');
      _handlePlaybackError();
    }
  }

  void playPrevious() async {
    if (radioStationPlayList.isEmpty || currentlyPlayingStation == null) {
      return;
    }

    emit(LoadingState());
    try {
      if (isInitialPlayListLoaded) {
        int prevIndex = getCurrentlyPlayingStationIndex() - 1;
        if (prevIndex >= 0) {
          // Safety check before changing
          if (_audioPlayer.playing) {
            await _audioPlayer.stop();
          }

          // Check if player is in a good state
          if (_audioPlayer.processingState == ProcessingState.idle ||
              _audioPlayer.processingState == ProcessingState.completed) {
            await _reinitializePlayer();

            // Seek directly to the previous index
            await _audioPlayer.setAudioSource(playlist!,
                initialIndex: prevIndex,
                initialPosition: Duration.zero,
                preload: false);
          } else {
            await _audioPlayer.seek(null, index: prevIndex);
          }

          await _audioPlayer.play();

          currentlyPlayingStation = radioStationPlayList[prevIndex];
          unawaited(_trackStationStart(currentlyPlayingStation!)); // Analytics
          emit(PlayingState());
        }
      } else {
        List<RadioStation> tempList = List.from(radioStationPlayList);
        int prevIndex = getCurrentlyPlayingStationIndex() - 1;

        if (prevIndex >= 0) {
          playRadioPlayList(
              stationToBePlayed: radioStationPlayList[prevIndex],
              allStations: tempList);
        }
      }
    } catch (e) {
      print('Error playing previous: $e');
      _handlePlaybackError();
    }
  }

  Future<void> pauseRadio() async {
    if (!_isAudioPlayerInitialized) return;
    try {
      print('DEBUG: pauseRadio() called');
      await _audioPlayer.pause();
      // End current listen session when pausing
      print('DEBUG: Calling endCurrentListenSession from pauseRadio');
      unawaited(_analytics.endCurrentListenSession());
      emit(StoppedState(isConnected: isConnectedCheck));
    } catch (e) {
      print('Error pausing radio: $e');
    }
  }

  void resumeRadio() {
    if (!_isAudioPlayerInitialized) return;
    print('DEBUG: resumeRadio() called - station: ${currentlyPlayingStation?.radioStationName}, playing: ${_audioPlayer.playing}, connected: $isConnectedCheck');
    if (currentlyPlayingStation != null &&
        !_audioPlayer.playing &&
        isConnectedCheck) {
      try {
        resumeRadioWithRetry();
        // Start new listen session when resuming
        print('DEBUG: Calling _trackStationStart from resumeRadio');
        unawaited(_trackStationStart(currentlyPlayingStation!));
      } catch (e) {
        print('Error resuming radio: $e');
        _handlePlaybackError();
      }
    }
  }

  void _listenPlayerStateStream() {
    _playerStateSubscription?.cancel();
    _playerStateSubscription =
        _audioPlayer.playerStateStream.listen((PlayerState playerState) {
      try {
        final processingState = playerState.processingState;
        if (processingState == ProcessingState.buffering ||
            processingState == ProcessingState.loading) {
          emit(LoadingState());
        } else if (processingState == ProcessingState.ready ||
            processingState == ProcessingState.idle) {
          // When notification is removed of the playing music then player state will be in idle state
          if (playerState.playing) {
            if (_audioPlayer.currentIndex != null &&
                _audioPlayer.currentIndex !=
                    getCurrentlyPlayingStationIndex() &&
                radioStationPlayList.isNotEmpty &&
                _audioPlayer.currentIndex! < radioStationPlayList.length) {
              currentlyPlayingStation =
                  radioStationPlayList[_audioPlayer.currentIndex!];
            }
            emit(PlayingState());
          } else {
            emit(StoppedState(isConnected: isConnectedCheck));
          }
        } else if (processingState != ProcessingState.completed) {
          if (_audioPlayer.currentIndex != null &&
              _audioPlayer.currentIndex != getCurrentlyPlayingStationIndex() &&
              radioStationPlayList.isNotEmpty &&
              _audioPlayer.currentIndex! < radioStationPlayList.length) {
            currentlyPlayingStation =
                radioStationPlayList[_audioPlayer.currentIndex!];
          }
          emit(PlayingState());
        } else {
          // Stream completed (AzuraCast timeout, stream ended, etc.)
          unawaited(_analytics.endCurrentListenSession());
          emit(StoppedState(isConnected: isConnectedCheck));
        }
      } catch (error) {
        print('Error in player state stream: $error');
        if (error is PlatformException) {
          _handlePlaybackError();
        }
      }
    });
  }

  void _listenSequenceStateStream() {
    _sequenceStateSubscription?.cancel();
    _sequenceStateSubscription = _audioPlayer.sequenceStateStream.listen(
      (sequenceState) {
        try {
          if (sequenceState == null) return;
          // Update current song title
          final currentItem = sequenceState.currentSource;
          if (currentItem?.tag == null) return;

          final MediaItem tag = currentItem!.tag as MediaItem;
          final stationId = tag.id;

          if (stationId != null && radioStationPlayList.isNotEmpty) {
            try {
              RadioStation musicData = radioStationPlayList.firstWhere(
                  (element) => element.radioStationId.toString() == stationId);

              if (currentlyPlayingStation != musicData) {
                currentlyPlayingStation = musicData;
                unawaited(_trackStationStart(musicData)); // Analytics
                emit(PlayingState());
              }
            } catch (e) {
              print('Station not found in playlist: $e');
            }
          }
        } catch (e) {
          print('Error in sequence state stream: $e');
        }
      },
    );
  }

  @override
  Future<void> close() async {
    try {
      // End any active listen session for analytics
      await _analytics.endCurrentListenSession();

      _retryTimer?.cancel();
      _connectivitySubscription?.cancel();
      _playerStateSubscription?.cancel();
      _sequenceStateSubscription?.cancel();

      // Only deactivate audio session, don't dispose the shared player
      await _audioSession?.setActive(false);
    } catch (e) {
      print('Error cleaning up resources: $e');
    }
    return super.close();
  }
}
