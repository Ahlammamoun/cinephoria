<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Utilisateur;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $passwordHasher;

    protected function setUp(): void
    {
        $this->client = static::createClient();  // Crée le client de test
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
    }

    public function testRegisterUserWithValidData()
    {
        // Simuler une requête POST avec des données valides
        $data = [
            'login' => 'testuser',
            'password' => 'password123',
            'prenom' => 'John',
            'nom' => 'Doe',
            'role' => 'user'
        ];

        $this->client->request('POST', '/api/register', [], [], [], json_encode($data));

        // Vérifiez que la réponse HTTP est 201 (créée)
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // Vérifiez que le message de succès est bien présent
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('User created successfully', $response['message']);

        // Vérifiez que l'utilisateur est bien enregistré dans la base de données
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['login' => 'testuser']);
        $this->assertNotNull($user);
        $this->assertEquals('testuser', $user->getLogin());

        // Vérifiez que le mot de passe est bien haché (il ne doit pas correspondre au mot de passe en clair)
        $this->assertNotEquals('password123', $user->getPassword());
    }

    public function testRegisterUserWithMissingFields()
    {
        // Simuler une requête POST avec des données manquantes
        $data = [
            'login' => 'testuser',  // Pas de 'password'
            'prenom' => 'John',
            'nom' => 'Doe',
            'role' => 'ROLE_USER'
        ];

        $this->client->request('POST', '/api/register', [], [], [], json_encode($data));

        // Vérifiez que la réponse HTTP est 400 (Bad Request)
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        // Vérifiez que le message d'erreur est bien présent
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Missing required fields', $response['error']);
    }

    public function testRegisterUserWithInvalidRole()
    {
        $data = [
            'login' => 'testuser',
            'password' => 'password123',
            'prenom' => 'John',
            'nom' => 'Doe',
            'role' => 'INVALID_ROLE',  // Rôle invalide
        ];
    
        $this->client->request('POST', '/api/register', [], [], [], json_encode($data));
    
        // Vérifie que la réponse est bien une erreur 400 Bad Request
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    
        // Vérifie le message d'erreur
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Invalid role', $response['error']);
    }
    
}
