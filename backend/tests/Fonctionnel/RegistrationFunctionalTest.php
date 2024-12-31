<?php
namespace App\Tests\Fonctionnel;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Utilisateur;  // Assurez-vous d'importer la classe Utilisateur
use App\Entity\Reservation;

class RegistrationFunctionalTest extends WebTestCase
{
    private $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        // Créer le client de test
        $this->client = static::createClient();

        // Initialiser l'EntityManager en récupérant à partir du conteneur de services
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        
        // Nettoyer la base de données avant chaque test pour éviter les conflits de login
        $this->cleanTestUsers();
    }
    
    public function testRegisterWithInvalidRole(): void
    {
        // Test logique ici
        $data = [
            'login' => 'invalidRoleUser',
            'password' => 'password123',
            'prenom' => 'Jane',
            'nom' => 'Doe',
            'role' => 'invalidRole',
        ];

        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $this->assertResponseStatusCodeSame(400); // Vérifier que le code HTTP est correct
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Invalid role', $response['error']);
    }

    public function testRegisterWithMissingFields(): void
    {
        $data = [
            'login' => 'missingFields',
            'password' => 'securePassword',
        ];

        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $this->assertResponseStatusCodeSame(400);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Missing required fields', $response['error']);
    }

    public function testRegisterWithDuplicateLogin(): void
    {
        // Données d'inscription
        $data = [
            'login' => 'testduplicateuser', // Un login spécifique pour tester la duplication
            'password' => 'securePassword123',
            'prenom' => 'Jane',
            'nom' => 'Doe',
            'role' => 'user',
        ];

        // Supprimer un utilisateur existant avec le même login pour éviter les doublons
        $this->entityManager->createQuery('DELETE FROM App\Entity\Utilisateur u WHERE u.login = :login')
            ->setParameter('login', $data['login'])
            ->execute();

        // Valider la suppression
        $this->entityManager->flush();
        $this->entityManager->clear(); // Nettoyer le cache de l'EntityManager

        // Créer un utilisateur avec un login unique
        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        // Vérifier que l'utilisateur a bien été créé
        $this->assertResponseStatusCodeSame(201); // HTTP 201 Created
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('User created successfully', $response['message']);

        // Réessayer avec le même login pour simuler la duplication
        $this->client->request(
            'POST',
            '/api/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        // Vérifier que l'erreur 409 est renvoyée (Conflit)
        $this->assertResponseStatusCodeSame(409); // HTTP 409 Conflict
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Login already exists', $response['error']);
    }


    // Méthode pour nettoyer uniquement les utilisateurs créés pendant les tests (avec un préfixe spécifique)
    private function cleanTestUsers(): void
    {
        // Supprimer les réservations liées à l'utilisateur
        $this->entityManager->createQuery('DELETE FROM App\Entity\Reservation r WHERE r.utilisateur IN (SELECT u.id FROM App\Entity\Utilisateur u WHERE u.login LIKE :login)')
            ->setParameter('login', 'test%')
            ->execute();

        // Supprimer uniquement les utilisateurs créés pendant les tests (avec un préfixe spécifique)
        $this->entityManager->createQuery('DELETE FROM App\Entity\Utilisateur u WHERE u.login LIKE :login')
            ->setParameter('login', 'test%')
            ->execute();

        // Assurez-vous que les suppressions sont bien appliquées
        $this->entityManager->flush();
        $this->entityManager->clear(); // Nettoyer le cache de l'EntityManager
    }
}





