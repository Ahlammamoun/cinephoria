import 'dart:convert';
import 'dart:io'; // Importation de dart:io pour Cookie
import 'package:dio/dio.dart';
import 'package:cookie_jar/cookie_jar.dart';
import 'package:path_provider/path_provider.dart'; // Import path_provider
import 'package:flutter/foundation.dart' show kIsWeb; // Import kIsWeb
import 'package:shared_preferences/shared_preferences.dart'; // Import shared_preferences

class AuthService {
  static const String loginUrl = 'http://172.22.179.225:8000/api/login';

  late Dio dio;
  late PersistCookieJar cookieJar;
  late SharedPreferences _prefs; // For web storage

  AuthService() {
    _initialize().then((_) {
      dio = Dio();
      dio.interceptors.add(
        InterceptorsWrapper(
          onRequest: (options, handler) async {
            var cookies = await loadCookies();
            if (cookies.isNotEmpty) {
              options.headers['cookie'] = cookies.join('; ');
            }
            return handler.next(options);
          },
          onResponse: (response, handler) async {
            await saveCookies(response.headers['set-cookie']);
            return handler.next(response);
          },
          onError: (e, handler) {
            return handler.next(e);
          },
        ),
      );
    });
  }

  Future<void> _initialize() async {
    if (kIsWeb) {
      // Use shared_preferences for web
      _prefs = await SharedPreferences.getInstance();
      print("SharedPreferences initialized for web.");
    } else {
      // Use path_provider for mobile
      try {
        Directory appDocDir = await getApplicationDocumentsDirectory();
        String appDocPath = appDocDir.path;
        print("Application Documents Directory: $appDocPath");
        cookieJar = PersistCookieJar(storage: FileStorage(appDocPath + "/.cookies/"));
        print("CookieJar initialized for mobile.");
      } catch (e) {
        print("Error initializing cookieJar: $e");
      }
    }
  }

  Future<void> login(String username, String password) async {
    try {
      final response = await dio.post(
        loginUrl,
        options: Options(
          headers: {'Content-Type': 'application/json'},
        ),
        data: {
          'login': username,
          'password': password,
        },
      );

      if (response.statusCode == 200) {
        print("Utilisateur authentifié !");
        // Gestion de la session utilisateur après l'authentification
      } else {
        print("Erreur de connexion : ${response.statusCode}");
        throw Exception('Échec de l\'authentification');
      }
    } catch (e) {
      print("Erreur lors de la connexion : $e");
      throw Exception('Erreur lors de la connexion');
    }
  }

  Future<List<String>> loadCookies() async {
    if (kIsWeb) {
      // Load cookies from shared_preferences for web
      return _prefs.getStringList('cookies') ?? [];
    } else {
      // Load cookies from cookieJar for mobile
      try {
        List<Cookie> cookies = await cookieJar.loadForRequest(Uri.parse(loginUrl));
        return cookies.map((cookie) => "${cookie.name}=${cookie.value}").toList();
      } catch (e) {
        print("Erreur lors du chargement des cookies: $e");
        return [];
      }
    }
  }

  Future<void> saveCookies(List<String>? cookies) async {
    if (cookies != null) {
      if (kIsWeb) {
        // Save cookies to shared_preferences for web
        await _prefs.setStringList('cookies', cookies);
        print("Cookies saved to SharedPreferences.");
      } else {
        // Save cookies to cookieJar for mobile
        try {
          List<Cookie> parsedCookies = cookies.map((cookie) {
            var split = cookie.split('=');
            return Cookie(split[0], split[1]);
          }).toList();
          await cookieJar.saveFromResponse(Uri.parse(loginUrl), parsedCookies);
          print("Cookies saved to CookieJar.");
        } catch (e) {
          print("Erreur lors de la sauvegarde des cookies: $e");
        }
      }
    }
  }
}