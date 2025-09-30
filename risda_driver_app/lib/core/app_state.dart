import 'package:flutter/foundation.dart';
import '../models/user.dart';

class AppState extends ChangeNotifier {
  User? _currentUser;
  String? _authToken;
  bool _isLoading = false;

  User? get currentUser => _currentUser;
  String? get authToken => _authToken;
  bool get isLoading => _isLoading;
  bool get isLoggedIn => _currentUser != null && _authToken != null;

  void setUser(User? user) {
    _currentUser = user;
    notifyListeners();
  }

  void setAuthToken(String? token) {
    _authToken = token;
    notifyListeners();
  }

  void setLoading(bool loading) {
    _isLoading = loading;
    notifyListeners();
  }

  void logout() {
    _currentUser = null;
    _authToken = null;
    notifyListeners();
  }
}
