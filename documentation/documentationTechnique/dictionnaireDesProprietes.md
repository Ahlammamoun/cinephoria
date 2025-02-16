| Entité       | Propriété               | Type de données     | Description                                                                                     |
|--------------|-------------------------|---------------------|-------------------------------------------------------------------------------------------------|
| **Film**     | id                      | `int`               | Identifiant unique du film.                                                                      |
|              | title                   | `string`            | Titre du film.                                                                                  |
|              | description             | `text`              | Description du film.                                                                            |
|              | releaseDate             | `datetime`          | Date de sortie du film.                                                                          |
|              | minimumAge              | `int`               | Âge minimum recommandé pour voir le film.                                                       |
|              | note                    | `float`             | Note globale du film.                                                                            |
|              | isFavorite              | `boolean|null`      | Si le film est favori (peut être `null`).                                                        |
| **Genre**    | id                      | `int`               | Identifiant unique du genre.                                                                    |
|              | name                    | `string`            | Nom du genre (ex. Action, Comédie, Drame).                                                      |
| **Incident** | id                      | `int`               | Identifiant unique de l'incident.                                                               |
|              | description             | `text`              | Description de l'incident.                                                                      |
|              | dateSignalement         | `datetime`          | Date à laquelle l'incident a été signalé.                                                      |
|              | resolu                  | `boolean`           | Statut de l'incident : résolu ou non.                                                           |
|              | salle_id                | `int`               | Référence à la salle liée à l'incident (clé étrangère).                                         |
| **Qualite**  | id                      | `int`               | Identifiant unique de la qualité.                                                               |
|              | name                    | `string`            | Nom de la qualité (ex. HD, 4K).                                                                  |
| **Reservation** | id                   | `int`               | Identifiant unique de la réservation.                                                           |
|              | nombreSieges            | `int`               | Nombre total de sièges réservés.                                                                 |
|              | siegesReserves          | `text`              | Sièges réservés (représenté comme texte ou JSON).                                                |
|              | prixTotal               | `float`             | Prix total de la réservation.                                                                   |
|              | utilisateur_id          | `int`               | Référence à l'utilisateur (clé étrangère).                                                      |
|              | seance_id               | `int`               | Référence à la séance (clé étrangère).                                                          |
| **Salle**    | id                      | `int`               | Identifiant unique de la salle.                                                                  |
|              | numero                  | `int`               | Numéro de la salle.                                                                              |
|              | capaciteTotale          | `int`               | Capacité totale de la salle.                                                                    |
|              | capacitePMR             | `int`               | Capacité pour personnes à mobilité réduite (PMR).                                                |
|              | qualite_id              | `int`               | Référence à la qualité de la salle (clé étrangère).                                              |
| **Seance**   | id                      | `int`               | Identifiant unique de la séance.                                                                |
|              | dateDebut               | `datetime`          | Date et heure du début de la séance.                                                            |
|              | dateFin                 | `datetime`          | Date et heure de la fin de la séance.                                                           |
|              | salle_id                | `int`               | Référence à la salle où se déroule la séance (clé étrangère).                                   |
|              | film_id                 | `int`               | Référence au film diffusé pendant la séance (clé étrangère).                                    |
| **Utilisateur** | id                   | `int`               | Identifiant unique de l'utilisateur.                                                            |
|              | login                   | `string`            | Email unique de l'utilisateur (sert d'identifiant).                                              |
|              | password                | `string`            | Mot de passe de l'utilisateur (doit être haché en base).                                         |
|              | prenom                  | `string`            | Prénom de l'utilisateur.                                                                        |
|              | nom                     | `string`            | Nom de l'utilisateur.                                                                           |
|              | role                    | `string`            | Rôle de l'utilisateur (par ex. `ROLE_USER`, `ROLE_ADMIN`).                                      |
|              | requiresPasswordChange  | `boolean`           | Si l'utilisateur doit changer son mot de passe.                                                 |
|              | reservations            | `Collection<Reservation>` | Liste des réservations associées à l'utilisateur.                                               |
