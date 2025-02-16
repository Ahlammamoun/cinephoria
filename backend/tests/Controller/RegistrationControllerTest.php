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
        // Generate a unique login each time
        $uniqueLogin = 'testuser_' . uniqid();
    
        // Prepare the user data with the unique login
        $data = [
            'login' => $uniqueLogin,
            'password' => 'password123',
            'prenom' => 'John',
            'nom' => 'Doe',
            'role' => 'user'
        ];
    
        // Simulate a POST request with the data
        $this->client->request('POST', '/api/register', [], [], [], json_encode($data));
    
        // Verify the HTTP response is 201 (Created)
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    
        // Verify that the success message is returned in the response
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('User created successfully', $response['message']);
    
        // Verify the user is registered in the database
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['login' => $uniqueLogin]);
        $this->assertNotNull($user);
        $this->assertEquals($uniqueLogin, $user->getLogin());
    
        // Verify that the password is hashed (it should not match the plain password)
        $this->assertNotEquals('password123', $user->getPassword());
    
        // You could also check that the hashed password matches the format used by your hashing algorithm
        // For example, if using bcrypt, the password should start with "$2y$" or similar
        $this->assertStringStartsWith('$2y$', $user->getPassword());  // Assuming bcrypt is used
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
