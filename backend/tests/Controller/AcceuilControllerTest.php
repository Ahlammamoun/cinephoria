<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AcceuilControllerTest extends WebTestCase
{
    public function testGetFilmsDernierMercredi()
    {
        $client = static::createClient();

        // Requête vers ton endpoint
        $client->request('GET', '/api/');

        // Vérifie que la réponse est 200 OK
        $this->assertResponseStatusCodeSame(200);

        // Vérifie que le format est JSON
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        // Décoder la réponse JSON
        $responseContent = json_decode($client->getResponse()->getContent(), true);

        // Vérifie que c'est un tableau
        $this->assertIsArray($responseContent);

        // Vérifie que la structure des données est correcte
        if (!empty($responseContent)) {
            foreach ($responseContent as $film) {
                $this->assertArrayHasKey('id', $film);
                $this->assertArrayHasKey('title', $film);
                $this->assertArrayHasKey('description', $film);
                $this->assertArrayHasKey('releaseDate', $film);
            }
        }
    }
}
