import 'dart:developer';

import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'package:radio_online/utility/app_info.dart';

class AdInterstitial {
  AdInterstitial.load() {
    if (_interstitialAd == null) {
      InterstitialAd.load(
        adUnitId: AppInfo().interstitialAdId,
        request: const AdRequest(),
        adLoadCallback: InterstitialAdLoadCallback(
          onAdLoaded: (ad) {
            _interstitialAd = ad;
          },
          onAdFailedToLoad: (err) {
            log(err.message);
          },
        ),
      );
    }
  }

  AdInterstitial.show() {
    if (_interstitialAd == null) {
      log('interstitial ad has not been loaded', name: 'AdInterstitial');
    } else {
      _interstitialAd!.show();
    }
    _interstitialAd = null;
  }

  static InterstitialAd? _interstitialAd;
}
