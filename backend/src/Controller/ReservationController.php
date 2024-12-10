<?php

namespace App\Controller;

use App\Entity\Seance;
use App\Entity\Reservation;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ReservationController extends AbstractController
{
    #[Route('/api/reservation', name: 'api_reservation', methods: ['POST'])]
    public function reserver(
        Request $request,
        EntityManagerInterface $em,
        Security $security
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Vérifier que l'utilisateur est authentifié
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Authentication required.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Vérifier les données reçues
        if (!isset($data['seance_id'], $data['nombre_sieges'], $data['sieges_reserves'])) {
            return new JsonResponse(['error' => 'Invalid data.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $seance = $em->getRepository(Seance::class)->find($data['seance_id']);
        if (!$seance) {
            return new JsonResponse(['error' => 'Seance not found.'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Vérifier la disponibilité des places
        $reservations = $seance->getReservations();
        $placesRestantes = $seance->getSalle()->getCapaciteTotale() - $reservations->reduce(function ($carry, $reservation) {
            return $carry + $reservation->getNombreSieges();
        }, 0);

        if ($placesRestantes < $data['nombre_sieges']) {
            return new JsonResponse(['error' => 'Not enough seats available.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Créer la réservation
        $reservation = new Reservation();
        $reservation->setSeances($seance);
        $reservation->setUtilisateur($user);
        $reservation->setNombreSieges($data['nombre_sieges']);
        $reservation->setSiegesReserves($data['sieges_reserves']);
        $reservation->setPrixTotal($data['nombre_sieges'] * $seance->getSalle()->getQualite()->getPrix()); // Exemple de calcul de prix

        $em->persist($reservation);
        $em->flush();

        return new JsonResponse(['message' => 'Reservation successful.'], JsonResponse::HTTP_CREATED);
    }
}
