<?php

namespace App\Controller;

use App\Entity\Seance;
use App\Entity\Reservation;
use App\Entity\Utilisateur;
use App\Entity\Cinema;
use App\Entity\Film;
use App\Repository\ReservationRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Repository\CinemaRepository;
use App\Repository\FilmRepository;
use App\Repository\SeanceRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ReservationController extends AbstractController
{
    #[Route('/api/reservation', name: 'api_reservation')]
    public function reservation(Request $request, CinemaRepository $CinemaRepository): JsonResponse
    {
        $cinemas = $CinemaRepository->findAll();
        // Transformez les entités en un format JSON approprié
        $cinemasData = array_map(function ($cinema) {
            return [
                'id' => $cinema->getId(),
                'nom' => $cinema->getNom(),
                'adresse' => $cinema->getAdresse(),
            ];
        }, $cinemas);

        return new JsonResponse($cinemasData);
    }


    #[Route('/api/reservation/films/{cinemaId}', name: 'api_reservation_films', methods: ['GET'])]
    public function films(int $cinemaId, CinemaRepository $cinemaRepository): JsonResponse
    {
        // Récupération du cinéma
        $cinema = $cinemaRepository->find($cinemaId);

        if (!$cinema) {
            return new JsonResponse(['error' => 'Cinéma introuvable'], 404);
        }

        // Liste pour stocker les films uniques
        $films = [];

        // Parcours des salles et des séances pour récupérer les films
        foreach ($cinema->getSalle() as $salle) {
            foreach ($salle->getSeances() as $seance) {
                $film = $seance->getFilms(); // Méthode correcte pour récupérer le film
                if ($film && !isset($films[$film->getId()])) {
                    $films[$film->getId()] = $film;
                }
            }
        }

        // Conversion en tableau JSON-friendly
        $filmData = array_map(function ($film) {
            return [
                'id' => $film->getId(),
                'title' => $film->getTitle(),
                'description' => $film->getDescription(),
                'releaseDate' => $film->getReleaseDate()?->format('Y-m-d'),
                'minimumAge' => $film->getMinimumAge(),
                'note' => $film->getNote(),
                'isFavorite' => $film->isFavorite(),
                'affiche' => $film->getAffiche(),
            ];
        }, array_values($films));
        // dump($filmData);
        // Retour JSON
        return new JsonResponse($filmData);
    }

    #[Route('/api/reservation/seances/{cinemaId}/{filmId}', name: 'api_reservation_seances', methods: ['GET'])]
    public function seances(
        int $cinemaId,
        int $filmId,
        CinemaRepository $cinemaRepository,
        FilmRepository $filmRepository,
        ReservationRepository $reservation
    ): JsonResponse {
        // Récupérer le cinéma et le film
        $cinema = $cinemaRepository->find($cinemaId);
        $film = $filmRepository->find($filmId);

        // Vérification que le cinéma et le film existent
        if (!$cinema || !$film) {
            return new JsonResponse(['error' => 'Cinema or Film not found'], 404);
        }

        // Récupérer les séances associées
        $seances = [];
        foreach ($cinema->getSalle() as $salle) {
            foreach ($salle->getSeances() as $seance) {
                // Filtrer les séances associées au film spécifié
                if ($seance->getFilms()->getId() === $filmId) {
                    // Liste complète des sièges (exemple : ["S1", "S2", ..., "S100"])
                    $totalSeats = $salle->getCapaciteTotale();
                    $allSeats = array_map(function ($index) {
                        return "S" . ($index + 1);
                    }, range(0, $totalSeats - 1));
                    // Déterminer les sièges réservés pour les handicapés (3 derniers sièges)
                    $handicapSeats = array_slice($allSeats, -3);

                    // Récupérer les sièges réservés
                    $reservedSeats = [];
                    foreach ($seance->getReservations() as $reservation) {
                        $seatNumbers = $reservation->getSiegesReserves(); // Supposons que cela retourne un tableau
                        if (is_array($seatNumbers)) {
                            $reservedSeats = array_merge($reservedSeats, $seatNumbers); // Fusionner les sièges dans $reservedSeats
                        } elseif (is_string($seatNumbers)) {
                            $reservedSeats[] = $seatNumbers; // Ajouter directement si c'est une chaîne unique
                        }
                    }
                    // Combiner les sièges réservés pour les handicapés et les autres réservés
                    $allReservedSeats = array_merge($reservedSeats, $handicapSeats);
                    // Calculer les sièges disponibles
                    $freeSeats = array_diff($allSeats, $reservedSeats);

                    // Ajouter les informations de la séance
                    $seances[] = [
                        'id' => $seance->getId(),
                        'dateDebut' => $seance->getDateDebut()->format('Y-m-d H:i'),
                        'dateFin' => $seance->getDateFin()->format('Y-m-d H:i'),
                        'qualite' => $seance->getQualite()->getName(),
                        'salle' => $salle->getNumero(),
                        'availableSeats' => count($freeSeats), // Nombre de sièges disponibles
                        'freeSeatsList' => array_values($freeSeats), // Liste des sièges disponibles
                        'handicapSeats' => $handicapSeats,
                    ];
                }
            }
        }

        // Si aucune séance n'est trouvée
        if (empty($seances)) {
            return new JsonResponse(['error' => 'No seances found for this film in this cinema'], 404);
        }

        // Retourner les données des séances
        return new JsonResponse([
            'success' => true,
            'data' => $seances,
        ], 200);
    }


    #[Route('/api/reservation/create', name: 'api_create_reservation', methods: ['POST'])]
    public function createReservation(
        Request $request,
        EntityManagerInterface $entityManager,
        SeanceRepository $seanceRepository,
        MailerInterface $mailer,
        UtilisateurRepository $utilisateurRepository,
    ): JsonResponse {


        $seance = $seanceRepository->find(1);
        // Vérifier si la séance existe
        if (!$seance) {
            return new JsonResponse(['error' => 'Seance not found'], 404);
        }


        $salle = $seance->getSalle();

        if (!$salle) {
            return new JsonResponse(['error' => 'Salle not found'], 404);
        }
        $totalSeats = range(1, $salle->getCapaciteTotale()); // Exemple : Si 100 sièges, génère [1, 2, ..., 100]

        // Récupérer les sièges déjà réservés
        $reservedSeats = [];
        foreach ($seance->getReservations() as $reservation) {
            $reservedSeats = array_merge($reservedSeats, $reservation->getSiegesReserves());
        }

        // Filtrer les sièges disponibles
        $availableSeats = array_diff($totalSeats, $reservedSeats);

        $data = json_decode($request->getContent(), true);
        // dump($data);
        // Récupération des paramètres de la requête
        $seanceId = $data['seanceId'] ?? null;
        $seats = $data['seats'] ?? [];
        $userId = $data['userId'] ?? null;
        $price = $data['price'] ?? null;


        if (!$seanceId || empty($seats) || !$userId) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        // Récupérer la séance
        $seance = $seanceRepository->find($seanceId);
        if (!$seance) {
            return new JsonResponse(['error' => 'Seance not found'], 404);
        }


        // Récupérer l'utilisateur
        $utilisateur = $utilisateurRepository->find($userId);
        if (!$utilisateur) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }
        // // Calculer le prix côté backend (au cas où)
        // $backendPrice = count($seats) * $seance->getQualite()->getPrix();

        // // Comparer le prix envoyé avec celui calculé côté backend
        // if ($price !== $backendPrice) {
        //     return new JsonResponse(['error' => 'Price mismatch'], 400);
        // }

        // Vérifier la disponibilité des sièges
        $availableSeats = $seance->getSalle()->getCapaciteTotale() - count($seance->getReservations());
        if (count($seats) > $availableSeats) {
            return new JsonResponse(['error' => 'Not enough available seats'], 400);
        }

        // Créer une réservation
        $reservation = new Reservation();
        $reservation->setUtilisateur($utilisateur);
        $reservation->setSeances($seance);
        $reservation->setNombreSieges(count($seats));
        $reservation->setSiegesReserves($seats);
        $reservation->setPrixTotal($price);
        $reservation->setDateReservation(new \DateTime()); // Ajoute la date actuelle

        // Sauvegarder la réservation
        $entityManager->persist($reservation);
        $entityManager->flush();



        // $emailMessage = (new Email())
        //     ->from('noreply@cinema.com') // Adresse expéditeur
        //     ->to($email) // Adresse du destinataire
        //     ->subject('Confirmation de votre réservation')
        //     ->html("
        //     <h1>Votre réservation est confirmée !</h1>
        //     <p>Merci d'avoir réservé avec notre service.</p>
        //     <p><strong>Réservation ID :</strong> {$reservation->getId()}</p>
        //     <p><strong>Sièges :</strong> " . implode(', ', $seats) . "</p>
        //     <p><strong>Prix total :</strong> {$price} €</p>
        // ");

        // $mailer->send($emailMessage); // Envoi de l'e-mail

        return new JsonResponse([
            'success' => true,
            'reservationId' => $reservation->getId(),
            'priceValidated' => $price,
            'availableSeats' => $availableSeats,
        ], 201);
    }

    #[Route('/api/commandes', name: 'api_commandes', methods: ['GET'])]
    public function getUserReservations(SessionInterface $session, EntityManagerInterface $em): JsonResponse
    {
        $loggedInUser = $session->get('user');

        if (!$loggedInUser) {
            return new JsonResponse(['error' => 'User not authenticated'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $user = $em->getRepository(Utilisateur::class)->findOneBy(['login' => $loggedInUser['login']]);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Récupérer les réservations liées à cet utilisateur
        $reservations = $em->getRepository(Reservation::class)->findBy(['utilisateur' => $user]);

        if (!$reservations) {
            return new JsonResponse(['error' => 'No reservations found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $reservationsData = array_map(function ($reservation) {
            return [
                'id' => $reservation->getId(),
                'seance' => [
                    'id' => $reservation->getSeances()->getId(),
                    'dateDebut' => $reservation->getSeances()->getDateDebut()->format('Y-m-d H:i'),
                    'dateFin' => $reservation->getSeances()->getDateFin()->format('Y-m-d H:i'),
                    'film' => $reservation->getSeances()->getFilms()->getTitle(),
                    'salle' => $reservation->getSeances()->getSalle()->getNumero(),
                ],
                'sieges' => $reservation->getSiegesReserves(),
                'prixTotal' => $reservation->getPrixTotal(),
            ];
        }, $reservations);

        return new JsonResponse(['reservations' => $reservationsData], JsonResponse::HTTP_OK);
    }




    #[Route('/api/commandes/{id}/note', name: 'api_commandes_note', methods: ['POST'])]
    public function rateMovie(
        int $id,
        Request $request,
        SessionInterface $session,
        EntityManagerInterface $em
    ): JsonResponse {
        // Vérifie si l'utilisateur est connecté
        $loggedInUser = $session->get('user');
        if (!$loggedInUser) {
            return new JsonResponse(['error' => 'User not authenticated'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Récupérer l'utilisateur connecté
        $user = $em->getRepository(Utilisateur::class)->findOneBy(['login' => $loggedInUser['login']]);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Récupérer la réservation
        $reservation = $em->getRepository(Reservation::class)->find($id);
        if (!$reservation || $reservation->getUtilisateur() !== $user) {
            return new JsonResponse(['error' => 'Reservation not found or access denied'], JsonResponse::HTTP_FORBIDDEN);
        }

        // Vérifier si la séance est terminée
        $seance = $reservation->getSeances();
        $now = new \DateTime();
        if ($seance->getDateFin() > $now) {
            return new JsonResponse(['error' => 'You can only rate after the session ends'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Récupérer la note soumise
        $data = json_decode($request->getContent(), true);
        $note = $data['note'] ?? null;

        if ($note === null || $note < 1 || $note > 5) {
            return new JsonResponse(['error' => 'Invalid rating. Must be between 1 and 5'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Mettre à jour la note du film
        $film = $seance->getFilms();
        $filmNote = $film->getNote();

        if ($filmNote === null) {
            $film->setNote($note); // Première note
        } else {
            // Calculer la moyenne des notes
            $film->setNote(($filmNote + $note) / 2); // Simplification pour éviter d'ajouter un tableau de notes
        }

        // Enregistrer les changements
        $em->persist($film);
        $em->flush();

        return new JsonResponse(['message' => 'Rating submitted successfully'], JsonResponse::HTTP_OK);
    }

    #[Route('/api/seances', name: 'api_seances', methods: ['GET'])]
    public function getFutureSeances(SessionInterface $session, EntityManagerInterface $em): JsonResponse
    {
        $loggedInUser = $session->get('user');

        if (!$loggedInUser) {
            return new JsonResponse(['error' => 'User not authenticated'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Récupérer l'utilisateur connecté
        $user = $em->getRepository(Utilisateur::class)->findOneBy(['login' => $loggedInUser['login']]);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Filtrer les réservations à partir d'aujourd'hui
        $now = new \DateTime();
        $reservations = $em->getRepository(Reservation::class)
            ->createQueryBuilder('r')
            ->join('r.seances', 's')
            ->where('r.utilisateur = :user')
            ->andWhere('s.dateDebut >= :now') // Filtrer les séances futures
            ->setParameter('user', $user)
            ->setParameter('now', $now)
            ->orderBy('s.dateDebut', 'ASC') // Trier par date de début
            ->getQuery()
            ->getResult();

        if (!$reservations) {
            return new JsonResponse(['error' => 'No upcoming reservations found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $reservationsData = array_map(function ($reservation) {
            $seance = $reservation->getSeances();
            $film = $seance->getFilms();
            $salle = $seance->getSalle();

            return [
                'id' => $reservation->getId(),
                'film' => [
                    'title' => $film->getTitle(),
                    'affiche' => $film->getAffiche(), // Affiche du film
                ],
                'date' => $seance->getDateDebut()->format('Y-m-d'),
                'heureDebut' => $seance->getDateDebut()->format('H:i'),
                'heureFin' => $seance->getDateFin()->format('H:i'),
                'salle' => $salle->getNumero(),
                'sieges' => $reservation->getSiegesReserves(),
            ];
        }, $reservations);

        return new JsonResponse(['seances' => $reservationsData], JsonResponse::HTTP_OK);
    }


}