import 'package:flutter/material.dart'; 
import '../ models/seance.dart';  // Fixed extra space before 'models'
import '../services/api_service.dart';  // Fixed extra space before 'services'
import ' qr_code_page.dart';  // Fixed extra space before 'qr_code_page.dart'

class SeancesPage extends StatefulWidget {
  @override
  _SeancesPageState createState() => _SeancesPageState();
}

class _SeancesPageState extends State<SeancesPage> {
  late Future<List<Seance>> seances;

  @override
  void initState() {
    super.initState();
    seances = ApiService().fetchSeances();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Séances du Jour'),
      ),
      body: FutureBuilder<List<Seance>>(
        future: seances,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return Center(child: CircularProgressIndicator());
          } else if (snapshot.hasError) {
            return Center(child: Text('Erreur: ${snapshot.error}'));
          } else if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return Center(child: Text('Aucune séance disponible.'));
          } else {
            List<Seance> seancesData = snapshot.data!;
            return ListView.builder(
              itemCount: seancesData.length,
              itemBuilder: (context, index) {
                final seance = seancesData[index];
                return ListTile(
                  leading: Image.network(seance.affiche),  // Ensure `affiche` exists in Seance class
                  title: Text(seance.nomFilm),  // Ensure `nomFilm` exists in Seance class
                  subtitle: Text('${seance.jour} - ${seance.heureDebut}'),  // Ensure `jour` and `heureDebut` exist in Seance class
                  onTap: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (context) => QrCodePage(billetId: seance.nomFilm),
                      ),
                    );
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
