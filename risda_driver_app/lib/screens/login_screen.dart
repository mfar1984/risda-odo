import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../theme/pastel_colors.dart';
import '../theme/text_styles.dart';
import '../services/auth_service.dart';
import 'dashboard_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _obscurePassword = true;
  String? _errorMessage;

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final isPortrait = MediaQuery.of(context).orientation == Orientation.portrait;
    final bgImage = isPortrait
        ? 'assets/login.gif'
        : 'assets/background-landscape.png';

    return Scaffold(
      body: Stack(
        children: [
          // Background Image
          Positioned.fill(
            child: Image.asset(
              bgImage,
              fit: BoxFit.cover,
              errorBuilder: (context, error, stackTrace) {
                return Container(
                  color: Colors.white,
                );
              },
            ),
          ),
          // Login Form
          SafeArea(
            child: Column(
              children: [
                Expanded(
                  child: LayoutBuilder(
                    builder: (context, constraints) {
                      return Column(
                        children: [
                          Expanded(
                            child: Align(
                              alignment: Alignment.bottomCenter,
                              child: Padding(
                                padding: const EdgeInsets.only(left: 24, right: 24, bottom: 100),
                                child: Form(
                                  key: _formKey,
                                  child: Container(
                                    constraints: const BoxConstraints(maxWidth: 320),
                                    child: Column(
                                      mainAxisSize: MainAxisSize.min,
                                      crossAxisAlignment: CrossAxisAlignment.stretch,
                                      children: [
                                        // Error Message
                                        if (_errorMessage != null)
                                          Container(
                                            padding: const EdgeInsets.all(8),
                                            margin: const EdgeInsets.only(bottom: 16),
                                            decoration: BoxDecoration(
                                              color: PastelColors.error,
                                              borderRadius: BorderRadius.circular(8),
                                            ),
                                            child: Text(
                                              _errorMessage!,
                                              style: AppTextStyles.bodyMedium.copyWith(
                                                color: PastelColors.errorText,
                                              ),
                                              textAlign: TextAlign.center,
                                            ),
                                          ),
                                        // Email Field
                                        _buildTextField(
                                          controller: _emailController,
                                          label: 'Email',
                                          icon: Icons.email_outlined,
                                          keyboardType: TextInputType.emailAddress,
                                          validator: (value) {
                                            if (value == null || value.isEmpty) {
                                              return 'Sila masukkan email anda';
                                            }
                                            if (!RegExp(r'^[\w\.-]+@([\w-]+\.)+[\w-]{2,4}$').hasMatch(value)) {
                                              return 'Sila masukkan email yang sah';
                                            }
                                            return null;
                                          },
                                        ),
                                        const SizedBox(height: 16),
                                        // Password Field
                                        _buildTextField(
                                          controller: _passwordController,
                                          label: 'Kata Laluan',
                                          icon: Icons.lock_outline,
                                          obscureText: _obscurePassword,
                                          suffixIcon: IconButton(
                                            icon: Icon(
                                              _obscurePassword
                                                  ? Icons.visibility_outlined
                                                  : Icons.visibility_off_outlined,
                                              color: PastelColors.textSecondary,
                                              size: 18,
                                            ),
                                            onPressed: () {
                                              setState(() {
                                                _obscurePassword = !_obscurePassword;
                                              });
                                            },
                                          ),
                                          validator: (value) {
                                            if (value == null || value.isEmpty) {
                                              return 'Sila masukkan kata laluan anda';
                                            }
                                            if (value.length < 6) {
                                              return 'Kata laluan mestilah sekurang-kurangnya 6 aksara';
                                            }
                                            return null;
                                          },
                                        ),
                                        const SizedBox(height: 24),
                                        // Login Button
                                        Consumer<AuthService>(
                                          builder: (context, authService, child) {
                                            return SizedBox(
                                              height: 44,
                                              child: ElevatedButton(
                                                onPressed: authService.isLoading ? null : _handleLogin,
                                                style: ElevatedButton.styleFrom(
                                                  backgroundColor: PastelColors.primary,
                                                  foregroundColor: Colors.white,
                                                  shape: RoundedRectangleBorder(
                                                    borderRadius: BorderRadius.circular(15),
                                                  ),
                                                  elevation: 2,
                                                ),
                                                child: authService.isLoading
                                                    ? const SizedBox(
                                                        height: 20,
                                                        width: 20,
                                                        child: CircularProgressIndicator(
                                                          strokeWidth: 2,
                                                          valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                                                        ),
                                                      )
                                                    : Text(
                                                        'Log Masuk',
                                                        style: AppTextStyles.button.copyWith(
                                                          color: Colors.white,
                                                          fontSize: 14,
                                                        ),
                                                      ),
                                              ),
                                            );
                                          },
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                              ),
                            ),
                          ),
                        ],
                      );
                    },
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTextField({
    required TextEditingController controller,
    required String label,
    required IconData icon,
    bool obscureText = false,
    TextInputType? keyboardType,
    Widget? suffixIcon,
    String? Function(String?)? validator,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(15),
        border: Border.all(
          color: PastelColors.border,
          width: 0.5,
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.02),
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: TextFormField(
        controller: controller,
        obscureText: obscureText,
        keyboardType: keyboardType,
        validator: validator,
        style: AppTextStyles.bodyLarge,
        decoration: InputDecoration(
          prefixIcon: Icon(
            icon,
            color: PastelColors.textSecondary,
            size: 18,
          ),
          suffixIcon: suffixIcon,
          border: InputBorder.none,
          contentPadding: const EdgeInsets.symmetric(
            horizontal: 12,
            vertical: 12,
          ),
          hintText: label,
          hintStyle: AppTextStyles.bodyMedium.copyWith(
            color: PastelColors.textLight,
          ),
        ),
      ),
    );
  }

  Future<void> _handleLogin() async {
    setState(() {
      _errorMessage = null;
    });
    
    if (_formKey.currentState!.validate()) {
      final authService = context.read<AuthService>();
      
      final success = await authService.login(
        _emailController.text.trim(),
        _passwordController.text,
        rememberMe: true, // Always remember (removed checkbox)
      );

      if (!mounted) return;

      if (success) {
        // Navigate to dashboard on successful login
        Navigator.of(context).pushReplacement(
          MaterialPageRoute(builder: (_) => const DashboardScreen()),
        );
      } else {
        setState(() {
          // Show actual error from API or generic message
          _errorMessage = authService.lastErrorMessage ?? 'Email atau kata laluan tidak sah!';
        });
      }
    }
  }
}