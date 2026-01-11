import 'dart:math';

import 'package:flutter/material.dart';
import 'package:radio_online/repository/radio_station_repo.dart';
import 'package:radio_online/utility/app_localization.dart';

class ReportDialog extends StatefulWidget {
  const ReportDialog({required this.radioStationId, super.key});

  final int radioStationId;

  @override
  State<ReportDialog> createState() => _ReportDialogState();
}

class _ReportDialogState extends State<ReportDialog>
    with SingleTickerProviderStateMixin {
  final TextEditingController _reportController = TextEditingController();
  late final AnimationController _animationController;
  final _formkey = GlobalKey<FormFieldState<String>>();

  @override
  void initState() {
    super.initState();
    _animationController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 500),
    )..addListener(() {
        setState(() {});
      });
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      icon: Icon(
        Icons.report,
        color: Theme.of(context).colorScheme.error,
        size: 30,
      ),
      title: Text(AppLocalizations.getTranslatedLabel(context, 'report')),
      content: AnimatedBuilder(
        animation: _animationController,
        child: TextFormField(
          key: _formkey,
          validator: (value) => _reportController.text.isEmpty
              ? AppLocalizations.getTranslatedLabel(context, 'empty_mag')
              : null,
          maxLines: 3,
          controller: _reportController,
          decoration: InputDecoration(
            hintText: AppLocalizations.getTranslatedLabel(context, 'wrt_msg'),
          ),
        ),
        builder: (context, child) {
          final value = sin(2 * 2 * pi * _animationController.value);
          return Transform.translate(
            offset: Offset(value * 10, 0),
            child: child,
          );
        },
      ),
      actions: [
        TextButton(
          onPressed: () {
            if (mounted) {
              Navigator.pop(context);
            }
          },
          child: Text(AppLocalizations.getTranslatedLabel(context, 'cls')),
        ),
        TextButton(
          onPressed: () {
            if (_formkey.currentState?.validate() ?? false) {
              RadioStationRepo().reportStation(
                '${widget.radioStationId}',
                _reportController.text,
              );
              if (mounted) {
                Navigator.pop(context);
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text(
                      AppLocalizations.getTranslatedLabel(context, 'reported'),
                    ),
                    duration: const Duration(seconds: 1),
                  ),
                );
              }
            } else {
              _animationController.forward(from: 0);
            }
          },
          child: Text(AppLocalizations.getTranslatedLabel(context, 'done')),
        ),
      ],
    );
  }
}
