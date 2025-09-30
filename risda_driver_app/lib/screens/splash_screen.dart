import 'package:flutter/material.dart';
import 'login_screen.dart';
import 'dashboard_screen.dart';
import '../main.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({Key? key}) : super(key: key);

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> with SingleTickerProviderStateMixin {
  double _progress = 0;
  String _status = 'Memulakan...';
  int _displayPercent = 1;
  bool _percentDone = false;
  int _totalDurationMs = 0;
  late AnimationController _colorController;
  late Animation<Color?> _colorAnimation;
  final List<Color> _rainbowColors = [
    Color(0xFF5170FF), // blue
    Color(0xFF00C6FF), // cyan
    Color(0xFF00FFB4), // green
    Color(0xFFFFFF00), // yellow
    Color(0xFFFFA500), // orange
    Color(0xFFFF4B2B), // red
    Color(0xFFB721FF), // purple
  ];
  final List<String> _steps = [
    'Starting...',
    'Loading Framework...',
    'Checking Connection Status...',
    'Loading Local Data...',
    'Checking Login Status...',
    'Synchronizing Data...',
    'Loading Settings...',
    'Checking Device Security...',
    'Verifying Permissions...',
    'Loading User Preferences...',
    'Initializing Local Storage...',
    'Synchronizing Data...',
    'Preparing User Interface...',
    'Optimizing Performance...',
    'Finalizing...',
    'Done!'
  ];

  @override
  void initState() {
    super.initState();
    _colorController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 3),
    )..repeat();
    _colorAnimation = _colorController.drive(
      TweenSequence<Color?>([
        for (int i = 0; i < _rainbowColors.length; i++)
          TweenSequenceItem(
            tween: ColorTween(
              begin: _rainbowColors[i],
              end: _rainbowColors[(i + 1) % _rainbowColors.length],
            ),
            weight: 1,
          ),
      ]),
    );
    _totalDurationMs = (_steps.length * 600) + 400; // 600ms per step + 400ms final delay
    _startLoading();
    _startPercentCounter();
  }

  void _startPercentCounter() async {
    int durationMs = _totalDurationMs;
    int percent = 1;
    int interval = (durationMs / 100).floor();
    while (percent < 100 && mounted && !_percentDone) {
      await Future.delayed(Duration(milliseconds: interval));
      setState(() {
        _displayPercent = percent;
      });
      percent++;
    }
  }

  @override
  void dispose() {
    _colorController.dispose();
    super.dispose();
  }

  Future<void> _startLoading() async {
    for (int i = 0; i < _steps.length; i++) {
      setState(() {
        _status = _steps[i];
        _progress = (i + 1) / _steps.length;
      });
      
      // Perform actual initialization tasks at specific steps
      if (_steps[i].contains('Checking Connection Status')) {
        try {
          await connectivityService.checkConnectivity();
        } catch (e) {
          print('DEBUG: Error checking connectivity: $e');
          // Continue despite errors
        }
      } else if (_steps[i].contains('Synchronizing Data')) {
        try {
          final syncResult = await syncManager.syncNow();
          print('DEBUG: Sync result: ${syncResult['success'] ? 'Success' : 'Failed'} - ${syncResult['message']}');
        } catch (e) {
          print('DEBUG: Error syncing data: $e');
          // Continue despite errors
        }
      } else if (_steps[i].contains('Checking Login Status')) {
        // Check login status and refresh token if needed
        print('DEBUG: Checking login status in splash screen...');
        try {
          if (authRepository.isLoggedIn()) {
            print('DEBUG: User is logged in, checking token refresh...');
            await authRepository.refreshTokenIfNeeded();
          } else {
            print('DEBUG: User is not logged in');
          }
        } catch (e) {
          print('DEBUG: Error checking login status: $e');
          // Continue despite errors
        }
      }
      
      await Future.delayed(const Duration(milliseconds: 600));
    }
    
    await Future.delayed(const Duration(milliseconds: 400));
    
    if (mounted) {
      setState(() {
        _status = 'Done!';
        _displayPercent = 100;
        _percentDone = true;
      });
      
      // Navigate based on login status
      print('DEBUG: Final navigation check...');
      bool isLoggedIn = false;
      
      try {
        isLoggedIn = authRepository.isLoggedIn();
        print('DEBUG: Final login status: $isLoggedIn');
      } catch (e) {
        print('DEBUG: Error during final login check: $e');
        isLoggedIn = false; // Assume not logged in if there's an error
      }
      
      if (isLoggedIn) {
        print('DEBUG: Navigating to DashboardScreen');
        Navigator.of(context).pushReplacement(
          MaterialPageRoute(builder: (_) => const DashboardScreen()),
        );
      } else {
        print('DEBUG: Navigating to LoginScreen');
        Navigator.of(context).pushReplacement(
          MaterialPageRoute(builder: (_) => const LoginScreen()),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Stack(
        fit: StackFit.expand,
        children: [
          Image.asset(
            'assets/splash.gif',
            fit: BoxFit.cover,
          ),
          Container(
            color: Colors.black.withOpacity(0.15), // Slight overlay for contrast
          ),
          Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.end,
              children: [
                Padding(
                  padding: const EdgeInsets.only(bottom: 80.0), // Distance from bottom of screen
                  child: Column(
                    children: [
                      SizedBox(
                        width: 180,
                        child: AnimatedBuilder(
                          animation: _colorAnimation,
                          builder: (context, child) {
                            return LinearProgressIndicator(
                              value: _progress,
                              minHeight: 8,
                              backgroundColor: Colors.white24,
                              valueColor: AlwaysStoppedAnimation<Color>(_colorAnimation.value ?? Color(0xFF5170FF)),
                              borderRadius: BorderRadius.circular(8),
                            );
                          },
                        ),
                      ),
                      const SizedBox(height: 16),
                      AnimatedBuilder(
                        animation: _colorAnimation,
                        builder: (context, child) {
                          return Text(
                            '$_displayPercent%',
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                              color: _colorAnimation.value ?? Color(0xFF5170FF),
                            ),
                          );
                        },
                      ),
                      const SizedBox(height: 16),
                      AnimatedBuilder(
                        animation: _colorAnimation,
                        builder: (context, child) {
                          return Text(
                            _status,
                            style: TextStyle(
                              fontSize: 13,
                              color: _colorAnimation.value ?? Color(0xFF5170FF),
                            ),
                          );
                        },
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
} 