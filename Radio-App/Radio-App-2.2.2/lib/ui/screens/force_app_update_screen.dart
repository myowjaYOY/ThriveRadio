import 'package:flutter/material.dart';
import 'package:radio_online/utility/app_info.dart';
import 'package:radio_online/utility/app_localization.dart';
import 'package:url_launcher/url_launcher.dart';

class ForceAppUpdateScreen extends StatelessWidget {
  const ForceAppUpdateScreen({super.key});

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
            image: AssetImage('assets/images/logo.png'),
            fit: BoxFit.scaleDown,
            opacity: .2,
          ),
        ),
        child: Padding(
          padding: const EdgeInsetsDirectional.all(16),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.end,
            children: [
              Text(
                AppLocalizations.getTranslatedLabel(context, 'force_update'),
                style: Theme.of(context)
                    .textTheme
                    .titleLarge
                    ?.copyWith(color: Colors.white),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 20),
              Align(
                alignment: AlignmentDirectional.bottomCenter,
                child: ElevatedButton(
                  onPressed: () async {
                    final url = Uri.parse(AppInfo().appLink);
                    final canLaunch = await canLaunchUrl(url);
                    if (canLaunch) {
                      await launchUrl(url);
                    }
                  },
                  child: Text(
                    AppLocalizations.getTranslatedLabel(context, 'update_app'),
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
