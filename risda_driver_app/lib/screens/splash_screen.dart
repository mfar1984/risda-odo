import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../services/auth_service.dart';
import 'login_screen.dart';
import 'dashboard_screen.dart';

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
      if (mounted) {
        setState(() {
          _displayPercent = percent;
        });
      }
      percent++;
    }
  }

  @override
  void dispose() {
    _colorController.dispose();
    super.dispose();
  }

  Future<void> _startLoading() async {
    final authService = context.read<AuthService>();
    
    for (int i = 0; i < _steps.length; i++) {
      if (mounted) {
        setState(() {
          _status = _steps[i];
          _progress = (i + 1) / _steps.length;
        });
      }
      
      // Perform actual initialization tasks at specific steps
      if (_steps[i].contains('Checking Connection Status')) {
        // 🎨 DUMMY MODE - Skip connectivity check
        await Future.delayed(const Duration(milliseconds: 100));
      } else if (_steps[i].contains('Synchronizing Data')) {
        // 🎨 DUMMY MODE - Skip sync
        await Future.delayed(const Duration(milliseconds: 100));
      } else if (_steps[i].contains('Checking Login Status')) {
        // Initialize AuthService (check Hive for cached session)
        await authService.initialize();
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
      
      // Navigate based on authentication status from Hive
      final isAuthenticated = authService.isAuthenticated;
      
      if (isAuthenticated) {
        // User has cached session, auto-login to dashboard
        Navigator.of(context).pushReplacement(
          MaterialPageRoute(builder: (_) => const DashboardScreen()),
        );
      } else {
        // No cached session, show login screen
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
          // Background Image
          Image.asset(
            'assets/splash.gif',
            fit: BoxFit.cover,
            errorBuilder: (context, error, stackTrace) {
              return Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [
                      Color(0xFF2E7D32),
                      Color(0xFF4CAF50),
                    ],
                  ),
                ),
              );
            },
          ),
          // Slight overlay for contrast
          Container(
            color: Colors.black.withOpacity(0.15),
          ),
          // Progress UI
          Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.end,
              children: [
                Padding(
                  padding: const EdgeInsets.only(bottom: 80.0),
                  child: Column(
                    children: [
                      // Rainbow Progress Bar
                      SizedBox(
                        width: 180,
                        child: AnimatedBuilder(
                          animation: _colorAnimation,
                          builder: (context, child) {
                            return LinearProgressIndicator(
                              value: _progress,
                              minHeight: 8,
                              backgroundColor: Colors.white24,
                              valueColor: AlwaysStoppedAnimation<Color>(
                                _colorAnimation.value ?? Color(0xFF5170FF)
                              ),
                              borderRadius: BorderRadius.circular(8),
                            );
                          },
                        ),
                      ),
                      const SizedBox(height: 16),
                      // Percentage Counter
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
                      // Status Text
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