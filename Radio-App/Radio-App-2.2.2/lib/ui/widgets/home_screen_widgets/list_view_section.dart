import 'package:flutter/material.dart';
import 'package:radio_online/ui/widgets/home_screen_widgets/horizontal_list_view.dart';

import 'package:radio_online/utility/app_localization.dart';

class ListViewSection extends StatelessWidget {
  const ListViewSection({
    required this.title,
    required this.listData,
    required this.callback,
    super.key,
  });

  final String title;
  final List<dynamic> listData;
  final void Function() callback;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: MediaQuery.sizeOf(context).width,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Padding(
            padding: const EdgeInsetsDirectional.symmetric(horizontal: 12),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  title,
                  style: Theme.of(context).textTheme.titleLarge?.copyWith(
                        fontWeight: FontWeight.w600,
                      ),
                ),
                TextButton(
                  onPressed: callback,
                  child: Text(
                    AppLocalizations.getTranslatedLabel(context, 'see_more'),
                    style: Theme.of(context).textTheme.titleSmall!.copyWith(
                          color: Theme.of(context).primaryColor,
                          fontWeight: FontWeight.w600,
                        ),
                  ),
                ),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsetsDirectional.symmetric(horizontal: 4),
            child: HorizontalListView(data: listData),
          ),
        ],
      ),
    );
  }
}
