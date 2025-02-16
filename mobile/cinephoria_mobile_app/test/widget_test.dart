import 'dart:convert';
import 'package:flutter_test/flutter_test.dart';
import 'package:http/http.dart' as http;
import 'package:http/testing.dart';  // Pour créer un MockClient
import 'package:cinephoria_mobile_app/services/api_service.dart'; // Ton ApiService
import 'package:cinephoria_mobile_app/models/seance.dart';  // Remplace par le nom correct de ton package
// Le modèle Seance

void main() {
  group('ApiService.fetchSeances', () {
    // Test pour vérifier que fetchSeances fonctionne correctement quand l'API répond avec un code 200.
    test('retourne une liste de Seance lorsque la réponse est 200', () async {
      // Faux JSON simulé
      final fakeResponse = {
        'seances': [
          {
            'nomFilm': 'Film 1',
            'jour': 'Lundi',
            'heureDebut': '18:00',
            'affiche': 'http://example.com/affiche1.jpg'
          },
          {
            'nomFilm': 'Film 2',
            'jour': 'Mardi',
            'heureDebut': '20:00',
            'affiche': 'http://example.com/affiche2.jpg'
          }
        ]
      };

      // Créer un MockClient qui renvoie la fausse réponse.
      final client = MockClient((request) async {
        return http.Response(jsonEncode(fakeResponse), 200);  // Réponse avec code 200
      });

      final apiService = ApiService(client: client);  // Utilisation du MockClient

      // Appel de la méthode fetchSeances
      final seances = await apiService.fetchSeances();

      // Vérifier que le résultat est correct.
      expect(seances, isA<List<Seance>>());  // Doit être une liste de Seance
      expect(seances.length, 2);  // Il doit y avoir 2 séances
      expect(seances[0].nomFilm, 'Film 1');  // Le premier film doit être "Film 1"
      expect(seances[1].jour, 'Mardi');  // Le deuxième film doit avoir lieu "Mardi"
    });

    // Test pour vérifier le comportement en cas d'erreur (par exemple une erreur 404)
    test('lance une exception lorsque la réponse n\'est pas 200', () async {
      // Créer un MockClient qui renvoie une erreur 404
      final client = MockClient((request) async {
        return http.Response('Not Found', 404);  // Réponse avec code 404
      });

      final apiService = ApiService(client: client);

      // Vérifier que la méthode lance une exception.
      expect(apiService.fetchSeances(), throwsException);
    });
  });
}
