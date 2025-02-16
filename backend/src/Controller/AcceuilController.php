<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\FilmRepository;

class AcceuilController extends AbstractController
{
    #[Route('api/', name: 'app_acceuil')]
    public function getFilmsDernierMercredi(FilmRepository $filmRepository): JsonResponse
    {
        // Calculer la date du dernier mercredi
        $today = new \DateTime(); // Aujourd'hui
        $lastWednesday = (clone $today)->modify('last wednesday'); // Mercredi dernier
    
        // Si aujourd'hui est mercredi, ajuster pour qu'il soit inclus
        if ($today->format('w') === '3') { // '3' correspond à mercredi
            $lastWednesday = $today;
        }
    
        // Rechercher les films ajoutés depuis le dernier mercredi
        $films = $filmRepository->createQueryBuilder('f')
            ->where('f.releaseDate BETWEEN :lastWednesday AND :today')
            ->setParameter('lastWednesday', $lastWednesday->format('Y-m-d 00:00:00'))
            ->setParameter('today', $today->format('Y-m-d 23:59:59'))
            ->orderBy('f.releaseDate', 'DESC')
            ->getQuery()
            ->getResult();
    
        // Mapper les données pour la réponse JSON
        $filmData = array_map(function ($film) {
            return [
                'id' => $film->getId(),
                'title' => $film->getTitle(),
                'description' => $film->getDescription(),
                'releaseDate' => $film->getReleaseDate()?->format('Y-m-d'),
                'minimumAge' => $film->getMinimumAge(),
                'note' => $film->getNote(),
                'affiche' => $film->getAffiche(),
            ];
        }, $films);
    
        // Retourner les films en JSON
        return new JsonResponse($filmData);
    }
}
