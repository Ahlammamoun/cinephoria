import 'package:flutter/material.dart';
import 'package:cinephoria_mobile_app/services/api_service.dart';  // Importer le service ApiService
import 'package:cinephoria_mobile_app/models/seance.dart';  // Importer le modèle Seance

class SeancesPage extends StatelessWidget {
  // Méthode pour récupérer les séances via ApiService
  Future<List<Seance>> _fetchSeances() async {
    final apiService = ApiService();
    return await apiService.fetchSeances();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Séances du Jour'),
      ),
      body: FutureBuilder<List<Seance>>(
        future: _fetchSeances(),  // Appel de la méthode pour récupérer les séances
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return Center(child: CircularProgressIndicator());  // Afficher un indicateur de chargement
          } else if (snapshot.hasError) {
            return Center(child: Text('Erreur : ${snapshot.error}'));  // Afficher l'erreur
          } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return Center(child: Text('Aucune séance disponible.'));  // Si aucune séance
          } else {
            // Si les séances sont récupérées, on les affiche
            List<Seance> seances = snapshot.data!;
            return ListView.builder(
              itemCount: seances.length,
              itemBuilder: (context, index) {
                final seance = seances[index];
                return ListTile(
                  title: Text(seance.nomFilm),  // Affiche le nom du film
                  subtitle: Text('${seance.jour} à ${seance.heureDebut}'),  // Affiche le jour et l'heure
                  leading: Image.network(seance.affiche),  // Affiche l'affiche du film
                );
              },
            );
          }
        },
      ),
    );
  }
}
