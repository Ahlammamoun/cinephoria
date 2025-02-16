class Seance {
  final String nomFilm;
  final String jour;
  final String heureDebut;
  final String affiche;

  Seance({
    required this.nomFilm,
    required this.jour,
    required this.heureDebut,
    required this.affiche,
  });

  factory Seance.fromMap(Map<String, dynamic> map) {
    return Seance(
      nomFilm: map['nomFilm'],  // Replace with the correct field names from your API response
      jour: map['jour'],
      heureDebut: map['heureDebut'],
      affiche: map['affiche'],
    );
  }
}
