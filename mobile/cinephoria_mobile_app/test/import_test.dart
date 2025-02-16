import 'package:flutter_test/flutter_test.dart';
import 'package:http/http.dart' as http;
import 'package:http/testing.dart';  // Pour MockClient
import 'package:cinephoria_mobile_app/services/api_service.dart';  // ApiService

void main() {
  test('Vérifie si ApiService peut être instanciée', () {
    final apiService = ApiService(client: MockClient((request) async {
      return http.Response('{"seances": []}', 200);  // Simule une réponse vide
    }));

    // Vérifie que ApiService peut être instanciée sans problème
    expect(apiService, isA<ApiService>());
  });
}
