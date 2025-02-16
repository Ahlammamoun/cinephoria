import 'package:flutter/material.dart';
import 'package:cinephoria_mobile_app/services/auth_service.dart'; // Importer AuthService

class LoginPage extends StatefulWidget {
  @override
  _LoginPageState createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final _usernameController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _isLoading = false;

  Future<void> _login() async {
    final username = _usernameController.text;
    final password = _passwordController.text;

    if (username.isEmpty || password.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Veuillez entrer un identifiant et un mot de passe')),
      );
      return;
    }

    setState(() {
      _isLoading = true;
    });

    try {
      final authService = AuthService();
      await authService.login(username, password);

      // Une fois connecté, redirige l'utilisateur vers la page des séances
      Navigator.pushReplacementNamed(context, '/seances');
    } catch (e) {
      setState(() {
        _isLoading = false;
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Erreur de connexion : $e')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Connexion')),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            TextField(
              controller: _usernameController,
              decoration: InputDecoration(labelText: 'Identifiant'),
            ),
            TextField(
              controller: _passwordController,
              obscureText: true, // Cache le mot de passe
              decoration: InputDecoration(labelText: 'Mot de passe'),
            ),
            SizedBox(height: 20),
            ElevatedButton(
              onPressed: _isLoading ? null : _login,
              child: _isLoading
                  ? CircularProgressIndicator()
                  : Text('Se connecter'),
            ),
          ],
        ),
      ),
    );
  }
}
