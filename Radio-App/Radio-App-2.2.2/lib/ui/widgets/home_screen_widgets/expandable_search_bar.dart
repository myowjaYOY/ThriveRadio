import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:radio_online/cubits/landing_screen/landing_screen_cubit.dart';
import 'package:radio_online/utility/app_localization.dart';

class ExpandableSearchBar extends StatefulWidget {
  const ExpandableSearchBar({super.key});

  @override
  State<ExpandableSearchBar> createState() => _ExpandableSearchBarState();
}

class _ExpandableSearchBarState extends State<ExpandableSearchBar> {
  bool isExpanded = false;
  final TextEditingController searchController = TextEditingController();
  final FocusNode _focusNode = FocusNode();

  @override
  void initState() {
    super.initState();
    _focusNode.addListener(() {
      if (!_focusNode.hasFocus) {
        setState(() {
          isExpanded = false;
        });
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    searchController.clear();
    return AnimatedContainer(
      duration: const Duration(milliseconds: 500),
      width: isExpanded ? MediaQuery.sizeOf(context).width * 0.98 : null,
      child: Row(
        children: [
          if (isExpanded)
            Expanded(
              child: Padding(
                padding: EdgeInsetsDirectional.only(start: 50),
                child: TextField(
                  controller: searchController,
                  autofocus: true,
                  focusNode: _focusNode,
                  decoration: InputDecoration(
                    hintText:
                        AppLocalizations.getTranslatedLabel(context, 'find'),
                    border: InputBorder.none,
                    hintStyle: TextStyle(color: Colors.white),
                    isDense: true,
                  ),
                  textInputAction: TextInputAction.search,
                  onTapOutside: (event) {
                    setState(() {
                      isExpanded = false;
                    });
                  },
                  onSubmitted: (query) {
                    if (query.isNotEmpty) {
                      context
                          .read<LandingScreenCubit>()
                          .loadRadioScreenByQuery(query: query);
                    }
                    setState(() {
                      isExpanded = false;
                    });
                  },
                ),
              ),
            ),
          IconButton(
            onPressed: () => setState(() {
              isExpanded = !isExpanded;
            }),
            icon: Icon(
              isExpanded ? Icons.close : Icons.search,
              color: Colors.white,
            ),
          ),
        ],
      ),
    );
  }
}