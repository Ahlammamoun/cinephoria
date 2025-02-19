import 'package:flutter/material.dart';
import 'services/api_service.dart';

void main() {
  runApp(MyApp());
}

class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      home: LoginPage(),
    );
  }
}

class LoginPage extends StatefulWidget {
  @override
  _LoginPageState createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final ApiService _apiService = ApiService();
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();

  bool _isLoggedIn = false;

  void _login() async {
    bool success = await _apiService.loginWithSession(
      _emailController.text,
      _passwordController.text,
    );

    if (success) {
      setState(() {
        _isLoggedIn = true;
      });
      _fetchCommandes();
    } else {
      setState(() {
        _isLoggedIn = false;
      });
      print('Erreur de connexion');
    }
  }

  void _fetchCommandes() async {
    try {
      List<dynamic> commandes = await _apiService.fetchCommandes();
      print('Commandes : $commandes');
    } catch (e) {
      print('Erreur lors de la récupération des commandes : $e');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Login et Commandes'),
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            if (!_isLoggedIn) ...[
              TextField(
                controller: _emailController,
                decoration: InputDecoration(labelText: 'Email'),
              ),
              TextField(
                controller: _passwordController,
                obscureText: true,
                decoration: InputDecoration(labelText: 'Mot de passe'),
              ),
              SizedBox(height: 20),
              ElevatedButton(
                onPressed: _login,
                child: Text('Se connecter'),
              ),
            ] else ...[
              Text('Connexion réussie !'),
            ],
          ],
        ),
      ),
    );
  }
}
