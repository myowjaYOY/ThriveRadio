import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:radio_online/cubits/translation/local_lang_cubit.dart';
import 'package:radio_online/models/app_language.dart';
import 'package:radio_online/utility/app_localization.dart';
import 'package:radio_online/utility/hive_utility.dart';

void _changeLanguage(String languageCode, BuildContext context) {
  HiveUtility.setLanguage(languageCode);
  context.read<LocaleCubit>().setLocale(Locale(languageCode));
}

void showLanguageModalBottomSheet(
  BuildContext context,
  List<AppLanguage> appLanguages,
) {
  showModalBottomSheet<void>(
    context: context,
    builder: (BuildContext context) {
      return Container(
        padding: const EdgeInsets.all(16),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              AppLocalizations.getTranslatedLabel(context, 'lang_selection'),
              style: const TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 16),
            Expanded(
              child: ListView(
                shrinkWrap: true,
                children: appLanguages.map((language) {
                  return RadioListTile<String>(
                    title: Text(language.languageName),
                    value: language.languageCode,
                    groupValue: HiveUtility.language,
                    onChanged: (value) {
                      _changeLanguage(value!, context);
                      Navigator.pop(context);
                    },
                  );
                }).toList(),
              ),
            ),
          ],
        ),
      );
    },
  );
}
