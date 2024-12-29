<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AdminAddControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        // Initialise le client et l'EntityManager
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    }

    /**
     * Authentifie l'administrateur existant avec ID 69.
     */
    private function authenticateAdmin(SessionInterface $session): void
    {
        // Récupérer l'admin existant en base (ID 69)
        $adminUser = $this->entityManager
            ->getRepository(Utilisateur::class)
            ->find(69);

        if (!$adminUser) {
            throw new \Exception('Administrateur avec ID 69 introuvable.');
        }

        // Enregistrer l'admin dans la session
        $session->set('user', [
            'login' => $adminUser->getLogin(),
            'nom' => $adminUser->getNom(),
            'prenom' => $adminUser->getPrenom(),
            'role' => $adminUser->getRole(),
            'id' => $adminUser->getId(),
        ]);
        $session->save();
    }

    /**
     * Test pour ajouter un film avec un administrateur authentifié.
     */
    public function testAddFilmWithAdmin()
    {
        // Obtenir la session depuis le conteneur
        $session = self::getContainer()->get('session.factory')->createSession();

        // Authentifier l'admin existant
        $this->authenticateAdmin($session);

        // Simuler le cookie de session pour la requête
        $this->client->getCookieJar()->set(new \Symfony\Component\BrowserKit\Cookie($session->getName(), $session->getId()));


        
        // Données pour ajouter un film
        $data = [
            'title' => 'Test Movie',
            'description' => 'This is a test movie.',
            'releaseDate' => '2024-12-01',
            'minimumAge' => 12,
            'note' => 5,
            'affiche' => 'https://example.com/movie_poster.jpg',
            'genres' => [1], // Supposons que le genre avec ID 1 existe
            'seances' => [
                [
                    'dateDebut' => '2024-12-10T10:00',
                    'dateFin' => '2024-12-10T12:00',
                    'salleId' => 1,
                    'qualiteId' => 1
                ]
            ]
        ];

        // Envoyer la requête POST
        $this->client->request(
            'POST',
            '/api/add-film',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        // Vérifie la réponse HTTP
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // Vérifie le message de succès
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('success', $response);
        $this->assertEquals('Film et séances ajoutés avec succès', $response['success']);
    }

    /**
     * Test pour ajouter un film avec un utilisateur non-admin.
     */
    public function testAddFilmWithNonAdmin()
    {
        // Obtenir la session depuis le conteneur
        $session = self::getContainer()->get('session.factory')->createSession();

        // Simuler un utilisateur non-admin
        $session->set('user', [
            'login' => 'user',
            'nom' => 'User',
            'prenom' => 'Test',
            'role' => 'user',
            'id' => 99, // Supposons un ID utilisateur non-admin
        ]);
        $session->save();

        // Simuler le cookie de session
        $this->client->getCookieJar()->set(new \Symfony\Component\BrowserKit\Cookie($session->getName(), $session->getId()));

        // Données pour ajouter un film
        $data = [
            'title' => 'Test Movie',
            'description' => 'This is a test movie.',
            'releaseDate' => '2024-12-01',
            'minimumAge' => 12,
            'note' => 5,
            'affiche' => 'https://example.com/movie_poster.jpg',
            'genres' => [1]
        ];

        // Envoyer la requête POST
        $this->client->request(
            'POST',
            '/api/add-film',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        // Vérifie la réponse HTTP
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        // Vérifie l'erreur
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Accès interdit, vous devez être administrateur', $response['error']);
    }
}


