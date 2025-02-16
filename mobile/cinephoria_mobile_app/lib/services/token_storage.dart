import 'package:shared_preferences/shared_preferences.dart';

class TokenStorage {
  static const String tokenKey = 'auth_token';

  // Sauvegarder le token dans SharedPreferences
  Future<void> saveAuthToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(tokenKey, token);  // Sauvegarder le token
  }

  // Récupérer le token depuis SharedPreferences
  Future<String?> getAuthToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(tokenKey);  // Retourner le token stocké
  }

  // Supprimer le token (lors de la déconnexion)
  Future<void> deleteAuthToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(tokenKey);  // Supprimer le token
  }
}


