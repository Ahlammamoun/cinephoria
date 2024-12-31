<?php

namespace App\Tests\Fonctionnel;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthenticationFunctionalTest extends WebTestCase
{
    private $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        // Démarrer le client HTTP
        $this->client = static::createClient();
        // Récupérer l’EntityManager
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);

        // Optionnel : nettoyer la base si nécessaire
        // $this->cleanDatabase();

        // Créer ou retrouver un utilisateur de test
        $existingUser = $this->entityManager->getRepository(Utilisateur::class)
            ->findOneBy(['login' => 'testuser']);

        if (!$existingUser) {
            $user = new Utilisateur();
            $user->setLogin('testuser')
                 ->setPassword('password123') // si le hachage est désactivé ou commenté, ça ira
                 ->setPrenom('John')
                 ->setNom('Doe')
                 ->setRole('user');

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }

    public function testLoginWithValidCredentials(): void
    {
        // 1) Préparer les données de connexion
        $data = [
            'login' => 'testuser',
            'password' => 'password123'
        ];

        // 2) Envoyer la requête POST
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        // 3) Vérifier que la réponse est 200 ou 401 si le passwordHasher est actif
        $this->assertResponseStatusCodeSame(200);

        // 4) Vérifier le contenu JSON
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Authentication successful', $responseData['message']);
        $this->assertArrayHasKey('token', $responseData);
        $this->assertArrayHasKey('user', $responseData);

        // 5) Optionnel : vérifier le stockage en session
        // On peut vérifier que la session contient 'user'
        $sessionCookie = $this->client->getCookieJar()->get(
            $this->client->getContainer()->get('session.factory')->createSession()->getName()
        );
        $this->assertNotNull($sessionCookie, 'Session cookie non trouvé après login');
    }

    public function testLoginWithMissingLoginOrPassword(): void
    {
        // Cas 1 : manque le champ 'login'
        $data = ['password' => 'password123'];
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
        $this->assertResponseStatusCodeSame(400);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Missing login or password', $response['error']);

        // Cas 2 : manque le champ 'password'
        $data = ['login' => 'testuser'];
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
        $this->assertResponseStatusCodeSame(400);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Missing login or password', $response['error']);
    }

    public function testLoginWithInvalidCredentials(): void
    {
        // 1) Envoyer un login inexistant
        $data = [
            'login' => 'nonexistinguser', 
            'password' => 'whatever'
        ];
    
        // 2) Envoyer la requête
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
    
        // 3) Vérifier qu’on obtient 401
        $this->assertResponseStatusCodeSame(401);
    
        // 4) Vérifier le message
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Invalid credentials', $response['error']);
    }
    
    public function testLogout(): void
    {
        // 1) Se logguer pour avoir une session
        $data = ['login' => 'testuser', 'password' => 'password123'];
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
        $this->assertResponseStatusCodeSame(200);

        // 2) Envoyer la requête POST /api/logout
        $this->client->request('POST', '/api/logout');
        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Logout successful', $response['message']);

        // 3) Vérifier si la session est invalidée
        // On tente d'accéder à /api/check-session => doit être 401
        $this->client->request('GET', '/api/check-session');
        $this->assertResponseStatusCodeSame(401);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertFalse($response['authenticated']);
    }

    public function testCheckSessionWithNoSession(): void
    {
        // 1) On ne se loggue pas
        // 2) On appelle /api/check-session => doit renvoyer 401
        $this->client->request('GET', '/api/check-session');
        $this->assertResponseStatusCodeSame(401);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(false, $response['authenticated']);
    }

    public function testCheckSessionWithUser(): void
    {
        // 1) Se logguer
        $data = ['login' => 'testuser', 'password' => 'password123'];
        $this->client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));
        $this->assertResponseStatusCodeSame(200);

        // 2) GET /api/check-session => doit renvoyer {authenticated: true, user: {...}}
        $this->client->request('GET', '/api/check-session');
        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertTrue($response['authenticated']);
        $this->assertArrayHasKey('user', $response);
        $this->assertEquals('testuser', $response['user']['login']);
    }

    private function cleanDatabase(): void
    {
        // Ex : tout supprimer
        // $this->entityManager->createQuery('DELETE FROM App\Entity\Utilisateur')->execute();
        // $this->entityManager->flush();
    }
}
