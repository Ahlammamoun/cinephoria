import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:cookie_jar/cookie_jar.dart';
import 'package:cinephoria_mobile_app/models/seance.dart';

class ApiService {
  static const String apiUrl = 'http://172.22.179.225:8000/api/seances';

  final CookieJar cookieJar = CookieJar();

  Future<List<Seance>> fetchSeances() async {
    var cookies = await cookieJar.loadForRequest(Uri.parse(apiUrl));
    print("Cookies utilisés : ${cookies.join('; ')}");

    if (cookies.isEmpty) {
      throw Exception('Utilisateur non authentifié');
    }

    final response = await http.get(
      Uri.parse(apiUrl),
      headers: {
        'Content-Type': 'application/json',
        'cookie': cookies.join('; '),
      },
    );

    if (response.statusCode == 200) {
      final Map<String, dynamic> data = json.decode(response.body);
      final List<dynamic> seancesData = data['seances'];

      return seancesData.map((seance) => Seance.fromMap(seance)).toList();
    } else if (response.statusCode == 401) {
      throw Exception('Utilisateur non authentifié');
    } else {
      throw Exception('Erreur ${response.statusCode} lors de la récupération des séances');
    }
  }
}


