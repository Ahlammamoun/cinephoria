<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Utilisateur;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class AuthenticationControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $passwordHasher;
    private $JWTManager;
    private $mailer;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        $this->JWTManager = self::getContainer()->get(JWTTokenManagerInterface::class);
        $this->mailer = self::getContainer()->get(MailerInterface::class);
    }

    // Test login avec des identifiants valides
    public function testLoginWithValidCredentials()
    {
        $user = $this->createUser();

        $data = [
            'login' => 'testuser',
            'password' => 'password123',
        ];

        $this->client->request('POST', '/api/login', [], [], [], json_encode($data));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('token', $response);
        $this->assertEquals('Authentication successful', $response['message']);
    }

    // Test login avec des identifiants invalides
    public function testLoginWithInvalidCredentials()
    {
        $data = [
            'login' => 'invaliduser',
            'password' => 'wrongpassword',
        ];

        $this->client->request('POST', '/api/login', [], [], [], json_encode($data));

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Invalid credentials', $response['error']);
    }




   
    

    // Test de déconnexion
    public function testLogout()
    {
        $this->client->request('POST', '/api/logout');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Logout successful', $response['message']);
    }

    // Test de vérification de la session
    public function testCheckSession()
    {
        $user = $this->createUser();
        $this->client->request('POST', '/api/login', [], [], [], json_encode([
            'login' => 'testuser',
            'password' => 'password123',
        ]));

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $token = $response['token'];  // Récupérer le token pour authentifier la requête

        // Vérifier si la session est active
        $this->client->request('GET', '/api/check-session', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $token
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($response['authenticated']);
    }

    // Méthode utilitaire pour créer un utilisateur
    private function createUser()
    {
        $user = new Utilisateur();
        $user->setLogin('testuser');
        $user->setPrenom('John');
        $user->setNom('Doe');
        $user->setRole('ROLE_USER');
        $password = $this->passwordHasher->hashPassword($user, 'password123');
        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
