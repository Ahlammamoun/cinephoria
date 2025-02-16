<?php
namespace App\Tests\Fonctionnel;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Utilisateur;
use App\Entity\Reservation;
use App\Entity\Seance; // Assurez-vous que cette entité existe

class ReservationFunctionalTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {

        $this->client = static::createClient();
        // Récupérer l'EntityManager à partir du conteneur de services
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);

        // Nettoyer la base de données avant chaque test pour éviter les conflits de login
        $this->cleanTestUsers();
    }

    // Test 1 : Créer une réservation
    public function testCreateReservation()
    {
        // 1) Récupérer la séance (id=1)
        $seance = $this->entityManager->getRepository(Seance::class)->find(1);
        if (!$seance) {
            throw new \Exception('Séance non trouvée.');
        }

        // 2) Récupérer l’utilisateur (id=273)
        $utilisateur = $this->entityManager->getRepository(Utilisateur::class)->find(273);
        if (!$utilisateur) {
            throw new \Exception('Utilisateur non trouvé.');
        }

        // 3) Préparer les données (JSON) : seanceId, seats, userId, price
        $data = [
            'seanceId' => $seance->getId(),  // Contrôleur attend "seanceId"
            'seats' => ['S1', 'S2'],     // Contrôleur attend "seats" (tableau)
            'userId' => $utilisateur->getId(), // Contrôleur attend "userId"
            'price' => 20,               // Contrôleur attend "price"
        ];

        // 4) Envoyer la requête POST au format JSON
        $this->client->request(
            'POST',
            '/api/reservation/create',
            [],  // pas de query param
            [],  // pas de fichier
            ['CONTENT_TYPE' => 'application/json'],   // important
            json_encode($data)                        // encoder $data en JSON
        );

        // 5) Vérifier la réponse (on veut 201 Created)
        $this->assertResponseStatusCodeSame(201);

        // 6) Vérifier la réponse JSON
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('reservationId', $response);
    }





    // Test 2 : Vérifier les réservations d'un utilisateur
    public function testGetReservations(): void
    {
        // 1) Créer un utilisateur de test avec un login dynamique unique
        $user = new Utilisateur();
        $user->setLogin('testuser_' . uniqid()); // Login unique généré avec uniqid()
        $user->setPassword('securePassword123');
        $user->setPrenom('Jane');
        $user->setNom('Doe');
        $user->setRole('user');
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    
        // 2) Créer une réservation pour l'utilisateur
        $reservation = new Reservation();
        $reservation->setUtilisateur($user);
        $reservation->setSeances($this->entityManager->getRepository(Seance::class)->find(1)); // ID de la séance
        $reservation->setNombreSieges(20);
        $reservation->setPrixTotal(20);
        $reservation->setDateReservation(new \DateTime());
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();
    
        // 3) Récupérer l'utilisateur créé (pour s'assurer qu'il existe bien en base)
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy(['login' => $user->getLogin()]);
    
        // 4) Simuler une session utilisateur
        $session = self::getContainer()->get('session.factory')->createSession();
        $session->set('user', [
            'id'    => $user->getId(),
            'login' => $user->getLogin(),
            'role'  => 'user',
        ]);
        $session->save();
    
        // 5) Ajouter le cookie de session dans la requête
        $cookieJar = $this->client->getCookieJar();
        $cookieJar->set(new \Symfony\Component\BrowserKit\Cookie($session->getName(), $session->getId()));
    
        // 6) Effectuer la requête GET
        $this->client->request('GET', '/api/commandes');
    
        // 7) Vérifier que la réponse est réussie (code 2xx)
        $this->assertResponseIsSuccessful();
    
        // 8) Vérifier le contenu de la réponse
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        
        // Si l'API renvoie une clé 'error', on fait échouer le test avec un message explicite
        if (isset($responseData['error'])) {
            $this->fail('API returned error: ' . $responseData['error']);
        } else {
            $this->assertArrayHasKey('reservations', $responseData);
            $this->assertNotEmpty($responseData['reservations']);
        }
    }
    


    // Méthode pour nettoyer uniquement les utilisateurs créés pendant les tests
    private function cleanTestUsers(): void
    {
        // Supprimer tous les utilisateurs de test avec un préfixe de login 'testuser_' pour éviter de supprimer d'autres utilisateurs
        $this->entityManager->createQuery('DELETE FROM App\Entity\Utilisateur u WHERE u.login LIKE :login')
            ->setParameter('login', 'test%')
            ->execute();

        // Supprimer les réservations liées aux utilisateurs de test
        $this->entityManager->createQuery('DELETE FROM App\Entity\Reservation r WHERE r.utilisateur IN (SELECT u.id FROM App\Entity\Utilisateur u WHERE u.login LIKE :login)')
            ->setParameter('login', 'test%')
            ->execute();

        // Assurez-vous que les suppressions sont bien appliquées
        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}


