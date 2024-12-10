<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\FilmRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class FilmController extends AbstractController
{
    #[Route('api/films', name: 'api_films' , methods: ['GET'])]
    public function getFilms(FilmRepository $filmRepository): JsonResponse
    {

        $films = $filmRepository->findAll();

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
        }, $films);


        return new JsonResponse($filmData);

       
    }
}
