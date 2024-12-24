<?php

namespace App\Controller;

use App\Entity\Film;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class AdminAddController extends AbstractController
{
    #[Route('/api/add-film', name: 'api_add_film', methods: ['POST'])]

    public function addFilm(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, ): JsonResponse
    {
        try {

            // Vérifier si l'utilisateur est connecté
            $user = $session->get('user');
            // var_dump($user);
            // die();

            if (!$user) {
                return new JsonResponse(['error' => 'Non authentifié'], 401);
            }

            // Vérification du rôle ADMIN
            if (!isset($user['role']) || $user['role'] !== 'admin') {
                return new JsonResponse(['error' => 'Accès interdit, vous devez être administrateur'], 403);
            }

            // Vérifie si aujourd'hui est mercredi
            $today = new \DateTime();
            if ($today->format('w') !== '3') { // '3' correspond au mercredi
                return new JsonResponse(['error' => 'Les films ne peuvent être ajoutés que le mercredi.'], Response::HTTP_FORBIDDEN);
            }

            // Décoder les données reçues
            $data = json_decode($request->getContent(), true);

            // Validation des données
            if (!$data || !isset($data['title'], $data['description'], $data['releaseDate'], $data['minimumAge'], $data['note'], $data['affiche'])) {
                return new JsonResponse(['error' => 'Données invalides'], Response::HTTP_BAD_REQUEST);
            }

            // Créer un nouveau film
            $film = new Film();
            $film->setTitle($data['title']);
            $film->setDescription($data['description']);
            $film->setReleaseDate(new \DateTime($data['releaseDate']));
            $film->setMinimumAge($data['minimumAge']);
            $film->setNote($data['note']);

            $film->setAffiche($data['affiche']);

            // Sauvegarder le film
            $entityManager->persist($film);
            $entityManager->flush();

            // Retourner une réponse avec succès
            return new JsonResponse(['success' => 'Film ajouté avec succès'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
