import 'dart:async';

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter_widget_from_html/flutter_widget_from_html.dart';
import 'package:radio_online/repository/radio_station_repo.dart';
import 'package:radio_online/utility/app_localization.dart';
import 'package:radio_online/utility/error_widget.dart';

class LegalInfoScreen extends StatefulWidget {
  const LegalInfoScreen({
    required this.legalInfo,
    required this.screenTitle,
    super.key,
  });

  final String legalInfo;
  final String screenTitle;

  @override
  State<LegalInfoScreen> createState() => _LegalInfoScreenState();
}

class _LegalInfoScreenState extends State<LegalInfoScreen> {
  final _streamController = StreamController<String>();

  Future<void> _fetchInfo() async {
    try {
      final data = await RadioStationRepo().getLegalInfo(widget.legalInfo);
      _streamController.sink.add(data);
    } catch (e) {
      _streamController.sink.addError(e.toString());
    }
  }

  @override
  void initState() {
    super.initState();
    _fetchInfo();
  }

  @override
  void dispose() {
    _streamController.close();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.sizeOf(context);

    return Scaffold(
      appBar: AppBar(
        backgroundColor: Theme.of(context).primaryColor,
        foregroundColor: Theme.of(context).colorScheme.onTertiary,
        centerTitle: true,
        title: Text(widget.screenTitle),
        leading: IconButton(
          onPressed: Navigator.of(context).pop,
          icon: Icon(
            CupertinoIcons.left_chevron,
            color: Theme.of(context).colorScheme.onTertiary,
          ),
        ),
      ),
      body: Padding(
        padding: const EdgeInsetsDirectional.all(16),
        child: SizedBox(
          width: size.width,
          height: size.height,
          child: StreamBuilder(
            builder: (context, snapshot) {
              if (snapshot.connectionState == ConnectionState.active &&
                  snapshot.data == null) {
                final isNoInternet =
                    snapshot.error.toString().contains('SocketException');

                return QErrorWidget(
                  onTapRetry: _fetchInfo,
                  showTryAgain: isNoInternet,
                  error: isNoInternet
                      ? AppLocalizations.getTranslatedLabel(context, 'internet')
                      : snapshot.error as String? ??
                          AppLocalizations.getTranslatedLabel(
                              context, 'no_display'),
                );
              }

              switch ((snapshot.connectionState, snapshot.data)) {
                case (ConnectionState.waiting, _):
                  return SizedBox(
                    width: size.width,
                    height: size.height,
                    child: const Center(child: CircularProgressIndicator()),
                  );
                case (ConnectionState.done, null):
                  return Center(
                      child: Text(AppLocalizations.getTranslatedLabel(
                          context, 'no_display')));
                case (ConnectionState.active, final String info):
                  return SingleChildScrollView(
                    child: HtmlWidget(info),
                  );
                default:
                  return Center(
                      child: Text(AppLocalizations.getTranslatedLabel(
                          context, 'no_display')));
              }
            },
            stream: _streamController.stream,
          ),
        ),
      ),
    );
  }
}
