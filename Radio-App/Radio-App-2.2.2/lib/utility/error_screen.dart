import 'package:flutter/material.dart';
import 'package:flutter_svg/svg.dart';
import 'package:radio_online/utility/constants.dart';

class ErrorScreen extends StatefulWidget {
  const ErrorScreen({
    required this.onTapRetry,
    required this.error,
    this.showTryAgain = false,
    super.key,
  });

  final VoidCallback onTapRetry;
  final bool showTryAgain;
  final String error;

  @override
  State<ErrorScreen> createState() => _ErrorScreenState();
}

class _ErrorScreenState extends State<ErrorScreen> {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      home: Scaffold(
          backgroundColor: const Color(0xfffefefe),
          body: Center(
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
                  widget.error,
                  textAlign: TextAlign.center,
                  style: Theme.of(context).textTheme.bodyLarge,
                ),
                if (widget.showTryAgain) ...[
                  const SizedBox(height: 10),
                  Container(
                    decoration: BoxDecoration(
                      border: Border.all(width: 1, color: kPrimaryColor),
                    ),
                    child: TextButton(
                      onPressed: widget.onTapRetry,
                      child: Text(
                        'Try Again',
                        style: Theme.of(context).textTheme.bodyLarge,
                      ),
                    ),
                  ),
                ],
              ],
            ),
          )),
    );
  }
}
