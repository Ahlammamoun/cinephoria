import 'package:flutter/material.dart';
import 'package:cinephoria_mobile_app/pages/login_page.dart';
import 'package:cinephoria_mobile_app/pages/seances_page.dart';
import 'package:cinephoria_mobile_app/services/auth_service.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(MyApp());
}

class MyApp extends StatelessWidget {
  final AuthService authService = AuthService();

  Future<bool> isAuthenticated() async {
    await Future.delayed(Duration(seconds: 1)); // Simulate a delay to ensure proper initialization
    var cookies = await authService.loadCookies();
    print("Cookies récupérés : ${cookies.join('; ')}");
    return cookies.isNotEmpty;
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<bool>(
      future: isAuthenticated(),
      builder: (context, snapshot) {
        if (snapshot.connectionState == ConnectionState.waiting) {
          return MaterialApp(
            home: Scaffold(
              body: Center(
                child: CircularProgressIndicator(),
              ),
            ),
          );
        } else if (snapshot.hasError) {
          return MaterialApp(
            home: Scaffold(
              body: Center(
                child: Text("Erreur : ${snapshot.error}"),
              ),
            ),
          );
        } else {
          return MaterialApp(
            title: 'Cinephoria Mobile',
            theme: ThemeData(
              primarySwatch: Colors.blue,
            ),
            initialRoute: snapshot.data == true ? '/seances' : '/',
            routes: {
              '/': (context) => LoginPage(),
              '/seances': (context) => SeancesPage(),
            },
          );
        }
      },
    );
  }
}

