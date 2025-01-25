import 'dart:convert';
import 'package:http/http.dart' as http;
import '../ models/seance.dart';  // Assurez-vous que ce chemin est correct

class ApiService {
static const String apiUrl = 'http://172.22.179.225:8000/api/seances';


  // Si vous avez un jeton d'authentification, ajoutez-le ici.
  // Exemple avec un jeton JWT
  static const String authToken = 'YOUR_AUTH_TOKEN_HERE';  // Remplacez par le jeton réel
  
  // Modifié pour retourner une Future<List<Seance>> au lieu de List<Map<String, dynamic>>
  Future<List<Seance>> fetchSeances() async {
    final response = await http.get(
      Uri.parse(apiUrl),
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $authToken',  // Ajoutez l'en-tête d'authentification
      },
    );

    if (response.statusCode == 200) {
      final Map<String, dynamic> data = json.decode(response.body);
      final List<dynamic> seancesData = data['seances'];

      // Transformation de la liste des données en une liste d'objets Seance
      return seancesData.map((seance) => Seance.fromMap(seance)).toList();
    } else {
      throw Exception('Échec de la récupération des séances');
    }
  }
}
