<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class ReservationControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

    // Test pour récupérer les cinémas
    public function testGetCinemas()
    {
        $this->client->request('GET', '/api/reservation');
        
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertNotEmpty($response);  // Assurez-vous que la réponse contient des données
    }

    // Test pour récupérer les films dans un cinéma
    public function testGetFilmsForCinema()
    {
        // On suppose que le cinéma avec ID 1 existe dans la base de données
        $this->client->request('GET', '/api/reservation/films/1');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $response[0]);  // Vérifie que chaque film contient un id
    }

    // Test pour récupérer les séances pour un film dans un cinéma
    public function testGetSeancesForCinemaAndFilm()
    {
        // On suppose que les IDs 1 et 1 existent (cinéma 1 et film 1)
        $this->client->request('GET', '/api/reservation/seances/1/1');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $response['data'][0]);  // Vérifie que chaque séance a un ID
    }

    // Test pour créer une réservation
    public function testCreateReservation()
    {
        // Exemple de données pour la réservation
        $data = [
            'seanceId' => 1,  // ID de la séance
            'seats' => ['S1', 'S2'],  // Sièges réservés
            'userId' => 1,  // ID de l'utilisateur
            'price' => 20.00,  // Prix total
        ];

        $this->client->request('POST', '/api/reservation/create', [], [], [], json_encode($data));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('reservationId', $response);  // Vérifie que la réponse contient un ID de réservation
    }

    // Test pour récupérer les réservations d'un utilisateur
    public function testGetUserReservations()
    {
        // On suppose qu'un utilisateur est déjà connecté et que son identifiant est 1
        $this->client->request('GET', '/api/commandes', [], [], ['HTTP_Authorization' => 'Bearer ' . 'valid_token_here']);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('reservations', $response);  // Vérifie que la réponse contient les réservations
    }

    // Test pour évaluer un film (noter une réservation)
    public function testRateMovie()
    {
        $data = [
            'note' => 4  // Exemple de note
        ];

        // On suppose qu'une réservation existe avec ID 1
        $this->client->request('POST', '/api/commandes/1/note', [], [], [], json_encode($data));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Rating submitted successfully', $response['message']);
    }
}
