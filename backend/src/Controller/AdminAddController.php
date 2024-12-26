<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Seance;
use App\Entity\Qualite;
use App\Entity\Salle;
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
    public function addFilm(
        Request $request,
        EntityManagerInterface $entityManager,
        SessionInterface $session
    ): JsonResponse {
        try {
            // Vérifier si l'utilisateur est connecté et admin
            $user = $session->get('user');
            // var_dump($user);
            if (!$user || $user['role'] !== 'admin') {
                return new JsonResponse(['error' => 'Accès interdit, vous devez être administrateur'], 403);
            }

            // Vérifie si aujourd'hui est mercredi
            $today = new \DateTime();
            if ($today->format('w') !== '3') { // '3' correspond au mercredi
                return new JsonResponse(['error' => 'Les films ne peuvent être ajoutés que le mercredi.'], Response::HTTP_FORBIDDEN);
            }

            // Décoder les données reçues
            $data = json_decode($request->getContent(), true);

            // Validation des données principales du film
            if (
                !$data || !isset($data['title'], $data['description'], $data['releaseDate'], $data['minimumAge'], $data['note'], $data['affiche'])
            ) {
                return new JsonResponse(['error' => 'Données du film incomplètes.'], Response::HTTP_BAD_REQUEST);
            }

            // Créer un nouveau film
            $film = new Film();
            $film->setTitle($data['title']);
            $film->setDescription($data['description']);
            $film->setReleaseDate(new \DateTime($data['releaseDate']));
            $film->setMinimumAge($data['minimumAge']);
            $film->setNote($data['note']);
            $film->setAffiche($data['affiche']);

            $entityManager->persist($film);

            // Ajouter les séances associées
            if (isset($data['seances']) && is_array($data['seances'])) {
                foreach ($data['seances'] as $seanceData) {
                    // Vérifier les données de la séance
                    if (!isset($seanceData['dateDebut'], $seanceData['dateFin'], $seanceData['salleId'], $seanceData['qualiteId'])) {
                        return new JsonResponse(['error' => 'Données de séance invalides.'], Response::HTTP_BAD_REQUEST);
                    }

                    // Créer une nouvelle séance
                    $seance = new Seance();
                    $seance->setDateDebut(new \DateTime($seanceData['dateDebut']));
                    $seance->setDateFin(new \DateTime($seanceData['dateFin']));


                    // Associer la salle
                    $salle = $entityManager->getRepository(Salle::class)->find($seanceData['salleId']);
                    if (!$salle) {
                        return new JsonResponse(['error' => "Salle ID {$seanceData['salleId']} introuvable."], Response::HTTP_BAD_REQUEST);
                    }
                    $seance->setSalle($salle);

                    // Associer la qualité
                    $qualite = $entityManager->getRepository(Qualite::class)->find($seanceData['qualiteId']);
                    if (!$qualite) {
                        return new JsonResponse(['error' => "Qualité ID {$seanceData['qualiteId']} introuvable."], Response::HTTP_BAD_REQUEST);
                    }
                    $seance->setQualite($qualite);

                    // Associer la séance au film
                    $seance->setFilms($film);
                    $entityManager->persist($seance);
                }
            }

            // Sauvegarder le film et les séances
            $entityManager->flush();

            return new JsonResponse(['success' => 'Film et séances ajoutés avec succès'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/admin/edit-film-with-seances/{id}', name: 'admin_edit_film_with_seances', methods: ['PUT'])]
    public function editFilmWithSeances(
        $id,
        Request $request,
        EntityManagerInterface $entityManager,
        SessionInterface $session
    ): JsonResponse {
        // Vérification du rôle ADMIN
        $user = $session->get('user');
        if (!$user || $user['role'] !== 'admin') {
            return new JsonResponse(['error' => 'Accès interdit'], 403);
        }

        try {
            // Récupérer le film
            $film = $entityManager->getRepository(Film::class)->find($id);
            if (!$film) {
                return new JsonResponse(['error' => 'Film non trouvé'], 404);
            }

            // Décoder les données envoyées
            $data = json_decode($request->getContent(), true);

            // Validation des données principales du film
            if (!isset($data['title'], $data['description'], $data['releaseDate'], $data['minimumAge'], $data['note'], $data['affiche'])) {
                return new JsonResponse(['error' => 'Données du film incomplètes.'], 400);
            }

            // Mise à jour des informations du film
            $film->setTitle($data['title']);
            $film->setDescription($data['description']);
            $film->setReleaseDate(new \DateTime($data['releaseDate']));
            $film->setMinimumAge($data['minimumAge']);
            $film->setNote($data['note']);
            $film->setAffiche($data['affiche']);

            // Mise à jour des séances associées
            if (isset($data['seances']) && is_array($data['seances'])) {
                foreach ($data['seances'] as $seanceData) {
                    // Vérifier si la séance existe
                    $seance = $entityManager->getRepository(Seance::class)->find($seanceData['id']);
                    if (!$seance || !$film->getSeances()->contains($seance)) {
                        return new JsonResponse(['error' => "Séance ID {$seanceData['id']} introuvable."], 404);
                    }

                    // Mise à jour des informations de la séance
                    if (isset($seanceData['dateDebut'])) {
                        $seance->setDateDebut(new \DateTime($seanceData['dateDebut']));
                    }
                    if (isset($seanceData['dateFin'])) {
                        $seance->setDateFin(new \DateTime($seanceData['dateFin']));
                    }


                    // Mise à jour de la salle
                    if (isset($seanceData['salleId'])) {
                        $salle = $entityManager->getRepository(Salle::class)->find($seanceData['salleId']);
                        if ($salle) {
                            $seance->setSalle($salle);
                        } else {
                            return new JsonResponse(['error' => "Salle ID {$seanceData['salleId']} introuvable."], 404);
                        }
                    }

                    // Mise à jour de la qualité
                    if (isset($seanceData['qualiteId'])) {
                        $qualite = $entityManager->getRepository(Qualite::class)->find($seanceData['qualiteId']);
                        if ($qualite) {
                            $seance->setQualite($qualite);
                        } else {
                            return new JsonResponse(['error' => "Qualité ID {$seanceData['qualiteId']} introuvable."], 404);
                        }
                    }
                }
            }

            // Sauvegarde dans la base de données
            $entityManager->flush();

            // Préparer la réponse avec les informations mises à jour
            $seances = [];
            foreach ($film->getSeances() as $seance) {
                $seances[] = [
                    'id' => $seance->getId(),
                    'dateDebut' => $seance->getDateDebut()->format('Y-m-d\TH:i'),
                    'dateFin' => $seance->getDateFin()->format('Y-m-d\TH:i'),
                    'salle' => $seance->getSalle() ? $seance->getSalle()->getId() : null,
                    'qualite' => $seance->getQualite() ? $seance->getQualite()->getId() : null,
                ];
            }

            return new JsonResponse([
                'success' => 'Film et séances modifiés avec succès',
                'film' => [
                    'title' => $film->getTitle(),
                    'description' => $film->getDescription(),
                    'releaseDate' => $film->getReleaseDate()->format('Y-m-d'),
                    'minimumAge' => $film->getMinimumAge(),
                    'note' => $film->getNote(),
                    'affiche' => $film->getAffiche(),
                ],
                'seances' => $seances,
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }



    #[Route('/api/admin/delete-film/{id}', name: 'admin_delete_film', methods: ['DELETE'])]
    public function deleteFilm(
        $id,
        EntityManagerInterface $entityManager,
        SessionInterface $session
    ): JsonResponse {
        // Vérifier si l'utilisateur est un admin
        $user = $session->get('user');
        if (!$user || $user['role'] !== 'admin') {
            return new JsonResponse(['error' => 'Accès interdit'], 403);
        }

        try {
            // Récupérer le film
            $film = $entityManager->getRepository(Film::class)->find($id);
            if (!$film) {
                return new JsonResponse(['error' => 'Film non trouvé'], 404);
            }

            // Supprimer les séances associées
            $seances = $film->getSeances();
            foreach ($seances as $seance) {
                $entityManager->remove($seance);
            }

            // Supprimer le film
            $entityManager->remove($film);

            // Appliquer les modifications
            $entityManager->flush();

            return new JsonResponse(['success' => 'Film et ses séances supprimés avec succès']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }



    #[Route('/api/admin/list-films', name: 'admin_list_films', methods: ['GET'])]
    public function listFilms(EntityManagerInterface $entityManager): JsonResponse
    {
        $films = $entityManager->getRepository(Film::class)->findAll();

        $data = array_map(function ($film) {
            return [
                'id' => $film->getId(),
                'title' => $film->getTitle(),
            ];
        }, $films);

        return new JsonResponse($data);
    }


    #[Route('/api/admin/get-film-with-seances/{id}', name: 'admin_get_film_with_seances', methods: ['GET'])]
    public function getFilmWithSeances(
        $id,
        EntityManagerInterface $entityManager,
        SessionInterface $session
    ): JsonResponse {
        // Vérification du rôle ADMIN
        $user = $session->get('user');
        if (!$user || $user['role'] !== 'admin') {
            return new JsonResponse(['error' => 'Accès interdit'], 403);
        }

        // Récupérer le film
        $film = $entityManager->getRepository(Film::class)->find($id);
        if (!$film) {
            return new JsonResponse(['error' => 'Film non trouvé'], 404);
        }

        // Préparer les données des séances
        $seances = [];
        foreach ($film->getSeances() as $seance) {
            $seances[] = [
                'id' => $seance->getId(),
                'dateDebut' => $seance->getDateDebut()->format('Y-m-d\TH:i'),
                'dateFin' => $seance->getDateFin()->format('Y-m-d\TH:i'),

                'salle' => $seance->getSalle() ? $seance->getSalle()->getId() : null,
                'qualite' => $seance->getQualite() ? $seance->getQualite()->getId() : null,
            ];
        }

        // Retourner les informations du film et des séances
        return new JsonResponse([
            'title' => $film->getTitle(),
            'description' => $film->getDescription(),
            'releaseDate' => $film->getReleaseDate()->format('Y-m-d'),
            'minimumAge' => $film->getMinimumAge(),
            'note' => $film->getNote(),
            'affiche' => $film->getAffiche(),
            'seances' => $seances,
        ]);
    }

    #[Route('/api/admin/add-salle', name: 'admin_add_salle', methods: ['POST'])]
    public function addSalle(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): JsonResponse
    {
        // Vérification du rôle ADMIN
        $user = $session->get('user');
        if (!$user || $user['role'] !== 'admin') {
            return new JsonResponse(['error' => 'Accès interdit'], 403);
        }
        // Décoder les données reçues
        $data = json_decode($request->getContent(), true);

        // Validation des données reçues
        if (
            !isset($data['numero'], $data['capaciteTotale'], $data['capacitePMR'], $data['qualite'])
            || !is_numeric($data['numero'])
            || !is_numeric($data['capaciteTotale'])
            || !is_numeric($data['capacitePMR'])
            || !is_numeric($data['qualite']) // Vérifie que la qualité est un ID valide
        ) {
            return new JsonResponse(['error' => 'Données invalides'], 400);
        }

        // Vérifier si la qualité existe dans la base
        $qualite = $entityManager->getRepository(Qualite::class)->find($data['qualite']);
        if (!$qualite) {
            return new JsonResponse(['error' => 'Qualité non trouvée'], 400);
        }

        try {
            // Créer une nouvelle salle
            $salle = new Salle();
            $salle->setNumero((int) $data['numero']);
            $salle->setCapaciteTotale((int) $data['capaciteTotale']);
            $salle->setCapacitePMR((int) $data['capacitePMR']);
            $salle->setQualite($qualite);

            // Sauvegarder dans la base de données
            $entityManager->persist($salle);
            $entityManager->flush();

            return new JsonResponse(['success' => 'Salle ajoutée avec succès'], 201);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de l\'ajout de la salle.'], 500);
        }
    }


    #[Route('/api/admin/edit-salle/{id}', name: 'admin_edit_salle', methods: ['PUT'])]
    public function editSalle($id, Request $request, EntityManagerInterface $entityManager, SessionInterface $session): JsonResponse
    {
        // Vérification du rôle ADMIN
        $user = $session->get('user');
        if (!$user || $user['role'] !== 'admin') {
            return new JsonResponse(['error' => 'Accès interdit'], 403);
        }
        // Récupérer la salle à modifier
        $salle = $entityManager->getRepository(Salle::class)->find($id);
        if (!$salle) {
            return new JsonResponse(['error' => 'Salle non trouvée'], 404);
        }

        // Décoder les données reçues
        $data = json_decode($request->getContent(), true);
    //   dump($data);
        try {
            // Mise à jour des champs de la salle
            $salle->setCapaciteTotale($data['capaciteTotale'] ?? $salle->getCapaciteTotale());
            $salle->setCapacitePMR($data['capacitePMR'] ?? $salle->getCapacitePMR());
            $salle->setNumero($data['numero'] ?? $salle->getNumero());

            // Mise à jour de la qualité
            if (isset($data['qualite'])) {
                $qualite = $entityManager->getRepository(Qualite::class)->find($data['qualite']);
                if (!$qualite) {
                    return new JsonResponse(['error' => 'Qualité non trouvée'], 400);
                }
                $salle->setQualite($qualite);
            }

            // **NOUVEAU : Mise à jour des réparations**
            if (isset($data['reparation'])) {
                $salle->setReparations($data['reparation']); // Supposons un champ réparations dans l'entité Salle
            }
            // dump($salle->getReparations());
            // Enregistrement des modifications
            $entityManager->flush();

            return new JsonResponse(['success' => 'Salle modifiée avec succès']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la mise à jour.'], 500);
        }
    }

    #[Route('/api/admin/delete-salle/{id}', name: 'admin_delete_salle', methods: ['DELETE'])]
    public function deleteSalle($id, EntityManagerInterface $entityManager, SessionInterface $session): JsonResponse
    {
        // Vérification du rôle ADMIN
        $user = $session->get('user');
        if (!$user || $user['role'] !== 'admin') {
            return new JsonResponse(['error' => 'Accès interdit'], 403);
        }
        $salle = $entityManager->getRepository(Salle::class)->find($id);
        if (!$salle) {
            return new JsonResponse(['error' => 'Salle non trouvée'], 404);
        }

        $entityManager->remove($salle);
        $entityManager->flush();

        return new JsonResponse(['success' => 'Salle supprimée avec succès']);
    }

    #[Route('/api/admin/list-salles', name: 'admin_list_salles', methods: ['GET'])]
    public function listSalles(EntityManagerInterface $entityManager): JsonResponse
    {
        $salles = $entityManager->getRepository(Salle::class)->findAll();

        $data = array_map(function ($salle) {
            return [
                'id' => $salle->getId(),
                'numero' => $salle->getNumero(),
                'capaciteTotale' => $salle->getCapaciteTotale(),
                'capacitePMR' => $salle->getCapacitePMR(),
                'qualite' => $salle->getQualite()
                    ? ['id' => $salle->getQualite()->getId(), 'nom' => $salle->getQualite()->getName()]
                    : null, // Sérialiser correctement la relation
                'reparation' => $salle->getReparations(),
            ];
        }, $salles);


        return new JsonResponse($data);
    }

    #[Route('/api/admin/list-qualites', name: 'admin_list_qualites', methods: ['GET'])]
    public function listQualites(EntityManagerInterface $entityManager): JsonResponse
    {
        $qualites = $entityManager->getRepository(Qualite::class)->findAll();

        $data = array_map(function ($qualite) {
            return [
                'id' => $qualite->getId(),
                'nom' => $qualite->getName(), // Changez en fonction de votre modèle
                'prix' => $qualite->getPrix(),
            ];
        }, $qualites);

        return new JsonResponse($data);
    }

    #[Route('/api/admin/reservations-stats', name: 'admin_reservations_stats', methods: ['GET'])]
    public function getReservationStats(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): JsonResponse
    {

        // Vérification du rôle ADMIN
        $user = $session->get('user');
        if (!$user || $user['role'] !== 'admin') {
            return new JsonResponse(['error' => 'Accès interdit'], 403);
        }

        // Calculer la date 7 jours avant aujourd'hui
        $startDate = new \DateTime('-7 days');
        $endDate = new \DateTime('now');

        // Récupérer les statistiques des réservations
        $query = $entityManager->createQuery(
            'SELECT f.title as film, 
                    COUNT(r.id) as reservations, 
                    COALESCE(SUM(r.prixTotal), 0) as chiffreAffaire
             FROM App\Entity\Reservation r
             JOIN r.seances s
             JOIN s.films f
             WHERE r.dateReservation BETWEEN :startDate AND :endDate
             GROUP BY f.id
             ORDER BY reservations DESC'
        )
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);


        $stats = $query->getResult();

        return new JsonResponse($stats);
    }



}
