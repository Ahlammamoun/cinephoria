import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class ApiService {
  static const String apiUrl = 'http://172.22.179.225:8000/api';
  String? _jwtToken;  // Stocker le jeton JWT si nécessaire

  // Connexion avec email et mot de passe
  Future<bool> loginWithSession(String email, String password) async {
    try {
      final response = await http.post(
        Uri.parse('$apiUrl/login'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'login': email,
          'password': password,
        }),
      );

      if (response.statusCode == 200) {
        print('Connexion réussie');
        var responseBody = jsonDecode(response.body);
        _jwtToken = responseBody['token'];  // Stocker le jeton JWT
        return true;
      } else {
        print('Erreur de connexion : ${response.statusCode}');
        return false;
      }
    } catch (e) {
      throw Exception('Erreur lors de la connexion : $e');
    }
  }

  // Récupérer les réservations (commandes)
  Future<List<dynamic>> fetchCommandes() async {
    try {
      final response = await http.get(
        Uri.parse('$apiUrl/commandes'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $_jwtToken',  // Ajouter le JWT dans l'en-tête Authorization
        },
      );

      if (response.statusCode == 200) {
        return jsonDecode(response.body)['reservations'];
      } else {
        throw Exception('Erreur lors de la récupération des commandes : ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Erreur lors de la récupération des commandes : $e');
    }
  }
}

class CommandesPage extends StatefulWidget {
  @override
  _CommandesPageState createState() => _CommandesPageState();
}

class _CommandesPageState extends State<CommandesPage> {
  final ApiService _apiService = ApiService();
  bool _isLoggedIn = false;
  List<dynamic> _reservations = [];

  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();

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
      setState(() {
        _reservations = commandes;
      });
    } catch (e) {
      print('Erreur lors de la récupération des commandes : $e');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Commandes'),
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
              Expanded(
                child: ListView.builder(
                  itemCount: _reservations.length,
                  itemBuilder: (context, index) {
                    var reservation = _reservations[index];
                    return ListTile(
                      title: Text('Film: ${reservation['seance']['film']}'),
                      subtitle: Text('Date: ${reservation['seance']['dateDebut']} - ${reservation['seance']['dateFin']}'),
                      trailing: Text('Total: ${reservation['prixTotal']}€'),
                    );
                  },
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }
}

void main() {
  runApp(MaterialApp(
    home: CommandesPage(),
  ));
}





