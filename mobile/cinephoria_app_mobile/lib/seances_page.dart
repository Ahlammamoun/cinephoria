import 'package:flutter/material.dart';
import 'services/api_service.dart';

class SeancesPage extends StatelessWidget {
  final ApiService apiService = ApiService();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Commandes'),
      ),
      body: FutureBuilder<List<dynamic>>(
        future: apiService.fetchCommandes(),  // Envoie du cookie pour récupérer les commandes
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return Center(child: CircularProgressIndicator());
          } else if (snapshot.hasError) {
            return Center(child: Text('Erreur: ${snapshot.error}'));
          } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return Center(child: Text('Aucune commande trouvée'));
          } else {
            // Afficher les commandes
            final commandes = snapshot.data!;
            return ListView.builder(
              itemCount: commandes.length,
              itemBuilder: (context, index) {
                return ListTile(
                  title: Text(commandes[index]['seance']['film'] ?? 'Film inconnu'),
                  subtitle: Text('Date: ${commandes[index]['seance']['dateDebut']}'),
                  onTap: () {
                    print('Commande sélectionnée');
                  },
                );
              },
            );
          }
        },
      ),
    );
  }
}

