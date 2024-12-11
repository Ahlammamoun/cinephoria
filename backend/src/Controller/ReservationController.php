<?php

namespace App\Controller;

use App\Entity\Seance;
use App\Entity\Reservation;
use App\Entity\Utilisateur;
use App\Entity\Cinema;
use App\Entity\Film;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Repository\CinemaRepository;
use App\Repository\FilmRepository;
use App\Repository\SeanceRepository;

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
        FilmRepository $filmRepository
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
                    $seances[] = [
                        'id' => $seance->getId(),
                        'dateDebut' => $seance->getDateDebut()->format('Y-m-d H:i'),
                        'dateFin' => $seance->getDateFin()->format('Y-m-d H:i'),
                        'qualite' => $seance->getQualite()->getName(),
                        'salle' => $salle->getNumero(),
                        'availableSeats' => $salle->getCapaciteTotale() - count($seance->getReservations()),
                    ];
                }
            }
        }

        // Si aucune séance n'est trouvée
        if (empty($seances)) {
            return new JsonResponse(['error' => 'No seances found for this film in this cinema'], 404);
        }
        //   !  dump($seances);
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
        SeanceRepository $seanceRepository
    ): JsonResponse {


        $seance = $seanceRepository->find(1);
        $salle = $seance->getSalle();

        // dump($salle);

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
        // $reservation->setUtilisateur($userId);
        $reservation->setSeances($seance);
        $reservation->setNombreSieges(count($seats));
        $reservation->setSiegesReserves($seats);
        $reservation->setPrixTotal($price); 

        // Sauvegarder la réservation
        $entityManager->persist($reservation);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'reservationId' => $reservation->getId(),
            'priceValidated' => $price,
        ], 201);
    }




}