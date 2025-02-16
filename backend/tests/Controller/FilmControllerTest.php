<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class FilmControllerTest extends WebTestCase
{
    public function testGetFilmsWithFilters()
    {
        $client = static::createClient();

        // Test de la récupération des films avec un cinéma spécifique
        $client->request('GET', '/api/films?cinemaId=1');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('films', $response);
        foreach ($response['films'] as $film) {
            // Vérifiez que chaque film a une séance dans le cinéma avec id 1
            $this->assertEquals(1, $film['seances'][0]['salle']);
        }

        // Test de la récupération des films avec un genre spécifique
        $client->request('GET', '/api/films?genre=Action');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('films', $response);
        foreach ($response['films'] as $film) {
            // Vérifiez que chaque film a le genre "Action"
            $this->assertContains('Action', $film['genres']);
        }

        // Test de la récupération des films avec une date spécifique
        $client->request('GET', '/api/films?date=2024-01-01');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('films', $response);
        foreach ($response['films'] as $film) {
            // Vérifiez que chaque film a une séance dans la plage de dates spécifiée
            $this->assertGreaterThanOrEqual('2024-01-01 00:00', $film['seances'][0]['dateDebut']);
            $this->assertLessThanOrEqual('2024-01-01 23:59', $film['seances'][0]['dateFin']);
        }
    }

    public function testGetFilmsWithoutFilters()
    {
        $client = static::createClient();

        // Test de la récupération de tous les films sans filtre
        $client->request('GET', '/api/films');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('films', $response);
        $this->assertArrayHasKey('genres', $response);
        $this->assertArrayHasKey('cinemas', $response);
    }
}
