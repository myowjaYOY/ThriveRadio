import 'dart:io';

import 'package:flutter/material.dart';
import 'package:radio_online/utility/app_info.dart';
import 'package:radio_online/utility/app_localization.dart';

class MaintenanceScreen extends StatelessWidget {
  const MaintenanceScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.sizeOf(context);

    return MaterialApp(
      debugShowCheckedModeBanner: false,
      home: Container(
        width: size.width,
        height: size.height,
        decoration: BoxDecoration(
          color: AppInfo().lightPrimaryColor,
          image: const DecorationImage(
            image: AssetImage(
              'assets/images/logo.png',
            ),
            fit: BoxFit.scaleDown,
            opacity: .2,
          ),
        ),
        child: Padding(
          padding: const EdgeInsetsDirectional.all(16),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.end,
            children: [
              const Icon(
                Icons.construction,
                size: 100,
                color: Colors.yellow,
              ),
              Text(
                'We are currently under maintenance \n Please come back later..',
                style: Theme.of(context).textTheme.titleLarge?.copyWith(
                      color: Colors.white,
                    ),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 20),
              Align(
                alignment: AlignmentDirectional.bottomCenter,
                child: ElevatedButton(
                  onPressed: () => exit(0),
                  child: Text(
                    AppLocalizations.getTranslatedLabel(context, 'close'),
                    style: TextStyle(color: AppInfo().lightPrimaryColor),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
