import 'package:flutter/material.dart';
import 'package:flutter_svg/svg.dart';
import 'package:radio_online/utility/constants.dart';

class QErrorWidget extends StatelessWidget {
  const QErrorWidget({
    required this.onTapRetry,
    required this.showTryAgain,
    required this.error,
    super.key,
  });

  final VoidCallback onTapRetry;
  final bool showTryAgain;
  final String error;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        mainAxisSize: MainAxisSize.min,
        children: [
          Padding(
            padding: const EdgeInsets.all(8.0),
            child: SvgPicture.asset(
              'assets/images/no_internet_illustrator.svg',
            ),
          ),
          Text(
            error,
            textAlign: TextAlign.center,
            style: Theme.of(context).textTheme.bodyLarge,
          ),
          if (showTryAgain) ...[
            const SizedBox(height: 10),
            Container(
              decoration: BoxDecoration(
                border: Border.all(width: 1, color: kPrimaryColor),
              ),
              child: TextButton(
                onPressed: onTapRetry,
                child: Text(
                  'Try Again',
                  style: Theme.of(context).textTheme.bodyLarge,
                ),
              ),
            ),
          ],
        ],
      ),
    );
  }
}
