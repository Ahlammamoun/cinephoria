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

        $this->createAdminIfNeeded();
    }

    private function createAdminIfNeeded(): void
    {
        // Récupérer l'administrateur avec ID 69
        // Rechercher un administrateur existant
        $admin = $this->entityManager
            ->getRepository(Utilisateur::class)
            ->findOneBy(['role' => 'admin', 'login' => 'admin_69']); // Recherche un administrateur avec le login spécifique

        // Si l'administrateur n'existe pas, on le crée
        if (!$admin) {
            $admin = new Utilisateur();
            $admin->setLogin('admin_69');
            $admin->setPassword(password_hash('securePassword', PASSWORD_DEFAULT)); // Assurez-vous d'utiliser un mot de passe haché
            $admin->setRole('admin');
            $admin->setPrenom('Admin');
            $admin->setNom('Test');
            $this->entityManager->persist($admin);
            $this->entityManager->flush();
        }
    }

    /**
     * Authentifie l'administrateur existant avec ID 69.
     */
    private function authenticateAdmin(SessionInterface $session): void
    {
        // Rechercher un administrateur avec un login spécifique ou autre critère unique
        $adminUser = $this->entityManager
            ->getRepository(Utilisateur::class)
            ->findOneBy(['login' => 'admin_69']); // Cherche un admin avec un login spécifique

        // Si l'administrateur n'est pas trouvé
        if (!$adminUser) {
            throw new \Exception('Administrateur avec le login "admin_69" introuvable.');
        }

        // Enregistrer l'admin dans la session
        $session->set('user', [
            'login' => $adminUser->getLogin(),
            'nom' => $adminUser->getNom(),
            'prenom' => $adminUser->getPrenom(),
            'role' => $adminUser->getRole(),
            'id' => $adminUser->getId(),
        ]);

        $session->save(); // Save the session

        // Make sure the session is applied to the client
        $this->client->getCookieJar()->set(new \Symfony\Component\BrowserKit\Cookie($session->getName(), $session->getId()));
    }



    /**
     * Test pour ajouter un film avec un administrateur authentifié.
     */
    public function testAddFilmWithAdmin(): void
{
    // 1) Récupérer l’administrateur avec l’ID 282
    $admin = $this->entityManager->getRepository(Utilisateur::class)->find(282);

    if (!$admin) {
        throw new \Exception('Administrateur avec l’ID 282 introuvable.');
    }

    // 2) Simuler la connexion (approche session manuelle OU loginUser, selon votre logique)

    // A) Approche session manuelle (si votre contrôleur lit session->get('user')):
    $session = self::getContainer()->get('session.factory')->createSession();
    $session->set('user', [
        'login'  => $admin->getLogin(),
        'nom'    => $admin->getNom(),
        'prenom' => $admin->getPrenom(),
        'role'   => $admin->getRole(),
        'id'     => $admin->getId(),
    ]);
    $session->save();
    $this->client->getCookieJar()->set(
        new \Symfony\Component\BrowserKit\Cookie($session->getName(), $session->getId())
    );

    // -- OU --

    // B) Approche Security (si votre appli utilise les firewalls et getUser()):
    // $this->client->loginUser($admin);

    // 3) Préparer les données pour ajouter un film
    $data = [
        'title'       => 'Test Movie',
        'description' => 'This is a test movie.',
        'releaseDate' => '2024-12-01',
        'minimumAge'  => 12,
        'note'        => 5,
        'affiche'     => 'https://example.com/movie_poster.jpg',
        'genres'      => [1],
        'seances'     => [
            [
                'dateDebut' => '2024-12-10T10:00',
                'dateFin'   => '2024-12-10T12:00',
                'salleId'   => 1,
                'qualiteId' => 1
            ]
        ]
    ];

    // 4) Envoyer la requête POST
    $this->client->request(
        'POST',
        '/api/add-film',
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode($data)
    );

    // 5) Vérifier qu’on a bien un code 201
    $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

    // 6) Vérifier la réponse JSON
    $response = json_decode($this->client->getResponse()->getContent(), true);
    $this->assertArrayHasKey('success', $response);
    $this->assertEquals('Film et séances ajoutés avec succès', $response['success']);
}

    

    /**
     * Test pour ajouter un film avec un utilisateur non-admin.
     */
    public function testAddFilmWithNonAdmin()
    {
        // 1. Simuler un utilisateur qui n’est pas admin
        $session = self::getContainer()->get('session.factory')->createSession();
        $session->set('user', [
            'login'  => 'user',
            'nom'    => 'User',
            'prenom' => 'Test',
            'role'   => 'user',  // non-admin
            'id'     => 99,
        ]);
        $session->save();
    
        $this->client->getCookieJar()->set(
            new \Symfony\Component\BrowserKit\Cookie($session->getName(), $session->getId())
        );
    
        // 2. Envoyer la requête POST pour ajouter un film
        $data = [
            'title' => 'Test Movie',
            'description' => 'This is a test movie.',
            'releaseDate' => '2024-12-01',
            'minimumAge' => 12,
            'note' => 5,
            'affiche' => 'https://example.com/movie_poster.jpg',
            'genres' => [1]
        ];
        $this->client->request(
            'POST',
            '/api/add-film',
            [], 
            [], 
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );
    
        // 3. **Vérifier qu’on a bien 403** (et non pas 200 ou 201).
        $this->assertResponseStatusCodeSame(403);
    
        // 4. Vérifier que l’API renvoie bien le message d’erreur attendu
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Accès interdit, vous devez être administrateur', $response['error']);
    }
    
}


