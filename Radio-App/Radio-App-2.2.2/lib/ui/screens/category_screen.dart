import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:radio_online/cubits/landing_screen/landing_screen_cubit.dart';
import 'package:radio_online/cubits/landing_screen/landing_screen_state.dart';
import 'package:radio_online/utility/app_localization.dart';
import 'package:radio_online/utility/error_message_labels.dart';
import 'package:radio_online/utility/error_widget.dart';

class CategoryScreen extends StatelessWidget {
  const CategoryScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      onPopInvoked: (didPop) {
        if (didPop) return;
        if (Scaffold.of(context).isDrawerOpen) {
          Scaffold.of(context).closeDrawer();
        } else {
          context.read<LandingScreenCubit>().loadHomeScreen();
        }
      },
      child: Padding(
        padding: const EdgeInsetsDirectional.symmetric(horizontal: 12),
        child: BlocBuilder<LandingScreenCubit, LandingScreenState>(
          builder: (BuildContext context, LandingScreenState state) {
            final categoryState = state as CategoryScreenState;
            switch (categoryState.status) {
              case Status.initial:
                return const Center(
                  child: CircularProgressIndicator(),
                );
              case Status.loading:
                return const Center(
                  child: CircularProgressIndicator(),
                );
              case Status.error:
                final isNoInternet = state.errorMessage!.contains(noInternet);

                return QErrorWidget(
                  onTapRetry:
                      context.read<LandingScreenCubit>().loadCategoryScreen,
                  showTryAgain: isNoInternet,
                  error: isNoInternet
                      ? AppLocalizations.getTranslatedLabel(context, 'internet')
                      : state.errorMessage ??
                          AppLocalizations.getTranslatedLabel(
                              context, 'cat_retrieval'),
                );
              case Status.loaded:
                final data = categoryState.categoryList;
                final count = (state.hasMoreData ?? false)
                    ? data!.length + 1
                    : data!.length;
                return RefreshIndicator(
                  onRefresh: context.read<LandingScreenCubit>().refreshState,
                  child: GridView.count(
                    crossAxisCount: 3,
                    padding: const EdgeInsets.symmetric(vertical: 12),
                    crossAxisSpacing: 8,
                    mainAxisSpacing: 8,
                    children: List.generate(count, (index) {
                      if (index == (count - 1) &&
                          (state.hasMoreData ?? false)) {
                        return GestureDetector(
                          onTap: context
                              .read<LandingScreenCubit>()
                              .loadMoreCategories,
                          child: Card(
                            elevation: 5,
                            child: Center(
                                child: Text(AppLocalizations.getTranslatedLabel(
                                    context, 'load_more'))),
                          ),
                        );
                      }

                      return GestureDetector(
                        onTap: () {
                          context
                              .read<LandingScreenCubit>()
                              .loadRadioScreenByCategory(
                                categoryId: data[index].categoryId,
                                categoryName: data[index].categoryName,
                              );
                        },
                        child: Card(
                          elevation: 5,
                          child: Column(
                            children: [
                              Expanded(
                                child: SizedBox.expand(
                                  child: Image.network(
                                    data[index].imageUrl,
                                    fit: BoxFit.cover,
                                  ),
                                ),
                              ),
                              Padding(
                                padding: const EdgeInsetsDirectional.all(2),
                                child: Center(
                                  child: Text(
                                    data[index].categoryName,
                                    textAlign: TextAlign.center,
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                    style: Theme.of(context)
                                        .textTheme
                                        .titleSmall
                                        ?.copyWith(fontWeight: FontWeight.w600),
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      );
                    }),
                  ),
                );
            }
          },
        ),
      ),
    );
  }
}
