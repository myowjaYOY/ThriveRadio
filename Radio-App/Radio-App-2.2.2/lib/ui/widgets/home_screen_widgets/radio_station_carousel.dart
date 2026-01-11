import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:radio_online/cubits/player/player_cubit.dart';
import 'package:radio_online/models/slider_model.dart';
import 'package:radio_online/utility/constants.dart';

class RadioStationCarousel extends StatefulWidget {
  const RadioStationCarousel({required this.sliders, super.key});

  final List<SliderModel> sliders;

  @override
  State<RadioStationCarousel> createState() => _RadioStationCarouselState();
}

class _RadioStationCarouselState extends State<RadioStationCarousel> {
  int currentPage = 0;
  late final int pageCount;
  final PageController _pageController = PageController(viewportFraction: 0.9);
  late final Timer _timer;

  @override
  void initState() {
    super.initState();
    pageCount = widget.sliders.length - 1;
    _pageController.addListener(() {
      setState(() {});
    });
    _timer = Timer.periodic(const Duration(seconds: 3), _handleTimerCallback);
  }

  @override
  void dispose() {
    _timer.cancel();
    super.dispose();
  }

  void _handleTimerCallback(timer) {
    if (currentPage < pageCount) {
      _goToPage(currentPage + 1);
    } else {
      _goToPage(0);
    }
  }

  void _goToPage(int page) {
    if (page <= pageCount && page >= 0) {
      _pageController.animateToPage(
        page,
        duration: const Duration(seconds: 1),
        curve: Curves.ease,
      );
      currentPage = page;
    }
  }

  @override
  Widget build(BuildContext context) {
    final width = MediaQuery.sizeOf(context).width;

    if (widget.sliders.isEmpty) {
      return const SizedBox.shrink();
    }
    return SizedBox(
      width: width,
      height: width * .6,
      child: PageView.builder(
        physics: const NeverScrollableScrollPhysics(),
        itemCount: widget.sliders.length,
        controller: _pageController,
        itemBuilder: (context, index) {
          final scale = (currentPage == index) ? 1.0 : 0.8;
          void onTap() {
            context
                .read<PlayerCubit>()
                .playRadioFromId(widget.sliders[index].radioStationId);
          }

          return GestureDetector(
            onTap: onTap,
            onHorizontalDragEnd: (details) {
              final textDirection = Directionality.of(context);

              if (textDirection == TextDirection.ltr) {
                if (details.primaryVelocity! > 0) {
                  _goToPage(currentPage - 1);
                } else if (details.primaryVelocity! < 0) {
                  _goToPage(currentPage + 1);
                }
              } else if (textDirection == TextDirection.rtl) {
                if (details.primaryVelocity! > 0) {
                  _goToPage(currentPage + 1);
                } else if (details.primaryVelocity! < 0) {
                  _goToPage(currentPage - 1);
                }
              }
            },
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 500),
              transformAlignment: FractionalOffset.center,
              transform: Matrix4.diagonal3Values(1, scale, 1),
              margin: const EdgeInsetsDirectional.all(10),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(16),
                child: Stack(
                  fit: StackFit.expand,
                  alignment: AlignmentDirectional.center,
                  children: [
                    Image.network(
                      widget.sliders[index].imageUrl,
                      errorBuilder: (context, err, stack) {
                        return Image.asset(kFallbackImage);
                      },
                      fit: BoxFit.cover,
                    ),
                    Align(
                      alignment: AlignmentDirectional.bottomCenter,
                      child: Container(
                        width: width,
                        alignment: AlignmentDirectional.bottomStart,
                        decoration: const BoxDecoration(
                          gradient: LinearGradient(
                            begin: AlignmentDirectional.topCenter,
                            end: AlignmentDirectional.bottomCenter,
                            colors: [
                              Colors.transparent,
                              Colors.transparent,
                              Colors.transparent,
                              Colors.black45,
                              Colors.black,
                            ],
                          ),
                        ),
                        padding: const EdgeInsetsDirectional.only(
                          bottom: 5,
                          start: 10,
                        ),
                        child: Text(
                          widget.sliders[index].sliderTitle,
                          style: Theme.of(context)
                              .textTheme
                              .titleLarge
                              ?.copyWith(color: Colors.white),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          );
        },
      ),
    );
  }
}
