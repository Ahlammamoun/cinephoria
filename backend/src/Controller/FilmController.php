<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\FilmRepository;
use App\Repository\SeanceRepository;
use App\Repository\CinemaRepository;
use App\Repository\GenreRepository;
use App\Entity\Film;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
class FilmController extends AbstractController
{
    #[Route('/api/films', name: 'api_films', methods: ['GET'])]
    public function getFilms(
        FilmRepository $filmRepository, 
        GenreRepository $genreRepository, 
        CinemaRepository  $cinemaRepository, 
        Request $request
    ): JsonResponse {
        $cinemaId = $request->query->get('cinemaId');
        $genre = $request->query->get('genre');
        $date = $request->query->get('date');
    
        // Construire la requête dynamique pour les films
        $queryBuilder = $filmRepository->createQueryBuilder('f')
            ->leftJoin('f.seances', 's')
            ->leftJoin('s.salle', 'sl')
            ->leftJoin('s.qualite', 'q') 
            ->leftJoin('f.genres', 'g')
            ->addSelect('s', 'sl', 'g');
    
        if ($cinemaId) {
            $queryBuilder->andWhere('sl.cinema = :cinemaId')
                         ->setParameter('cinemaId', $cinemaId);
        }
    
        if ($genre) {
            $queryBuilder->andWhere('g.name = :genre')
                         ->setParameter('genre', $genre);
        }
    
        if ($date) {
            $queryBuilder->andWhere('s.dateDebut BETWEEN :start AND :end')
                         ->setParameter('start', (new \DateTime($date))->setTime(0, 0, 0))
                         ->setParameter('end', (new \DateTime($date))->setTime(23, 59, 59));
        }
    
        $films = $queryBuilder->getQuery()->getResult();
    
        // Mapper les données des films
        $filmData = array_map(function (Film $film) {
            return [
                'id' => $film->getId(),
                'title' => $film->getTitle(),
                'description' => $film->getDescription(),
                'releaseDate' => $film->getReleaseDate()?->format('Y-m-d'),
                'minimumAge' => $film->getMinimumAge(),
                'note' => $film->getNote(),
                'isFavorite' => $film->isFavorite(),
                'affiche' => $film->getAffiche(),
                'genres' => array_map(fn($genre) => $genre->getName(), $film->getGenres()->toArray()),
                'seances' => array_map(fn($seance) => [
                    'id' => $seance->getId(),
                    'dateDebut' => $seance->getDateDebut()->format('Y-m-d H:i'),
                    'dateFin' => $seance->getDateFin()->format('Y-m-d H:i'),
                    'salle' => $seance->getSalle()?->getNumero(),
                    'qualite' => $seance->getQualite()?->getName(),
                ], $film->getSeances()->toArray())
            ];
        }, $films);
    
        // Récupérer tous les genres
        $genres = $genreRepository->findAll();
        $genreData = array_map(fn($genre) => [
            'id' => $genre->getId(),
            'name' => $genre->getName(),
        ], $genres);
    
        // Récupérer toutes les salles (cinémas)
        $cinemas = $cinemaRepository->findAll();
        $cinemaData = array_map(fn($cinema) => [
            'id' => $cinema->getId(),
            'name' => $cinema->getNom(), // Si vos cinémas ont un nom
            'adresse' => $cinema->getAdresse(),
        ], $cinemas);
    
        // Retourner les films, genres et cinémas
        return new JsonResponse([
            'films' => $filmData,
            'genres' => $genreData,
            'cinemas' => $cinemaData,
        ]);
    }
    
}
