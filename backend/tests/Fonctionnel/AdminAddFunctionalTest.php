<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Utilisateur;
use App\Entity\Genre;
use App\Entity\Film;
use App\Entity\Salle;
use App\Entity\Qualite;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use App\DataFixtures\AppFixtures;

class AdminAddFunctionalTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $passwordHasher;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = self::getContainer()->get('doctrine')->getManager();

        $this->passwordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);

        // Purger la base de données
        $purger = new ORMPurger($this->entityManager);
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->purge();

        // Charger les fixtures
        $loader = new Loader();
        $loader->addFixture(new AppFixtures($this->passwordHasher));
        $executor->execute($loader->getFixtures());

        // Créer ou récupérer l'utilisateur administrateur
        $adminUser = $this->entityManager->getRepository(Utilisateur::class)
            ->findOneBy(['login' => 'admin@test.com']);

        // Authentifier l'utilisateur administrateur
        $this->client->loginUser($adminUser);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Fermer EntityManager
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testAddFilmSuccess(): void
    {
        // Préparer les données du film
        $filmData = [
            'title' => 'Film de Test',
            'description' => 'Description du film de test.',
            'releaseDate' => '2025-01-01',
            'minimumAge' => 13,
            'note' => 8,
            'affiche' => 'https://example.com/poster.jpg',
            'genres' => [1, 2], // Assurez-vous que ces genres existent dans la base de données de test
            'seances' => [
                [
                    'dateDebut' => '2025-01-10T14:00',
                    'dateFin' => '2025-01-10T16:00',
                    'salleId' => 1, // Assurez-vous que cette salle existe
                    'qualiteId' => 1, // Assurez-vous que cette qualité existe
                ],
                [
                    'dateDebut' => '2025-01-11T18:00',
                    'dateFin' => '2025-01-11T20:00',
                    'salleId' => 2,
                    'qualiteId' => 1,
                ],
            ],
        ];

        // Créer les genres, salles, et qualités nécessaires
        $genre1 = $this->entityManager->getRepository(Genre::class)->find(1);
        $genre2 = $this->entityManager->getRepository(Genre::class)->find(2);
        $qualite = $this->entityManager->getRepository(Qualite::class)->find(1);
        $salle1 = $this->entityManager->getRepository(Salle::class)->find(1);
        $salle2 = $this->entityManager->getRepository(Salle::class)->find(2);

        $this->assertNotNull($genre1);
        $this->assertNotNull($genre2);
        $this->assertNotNull($qualite);
        $this->assertNotNull($salle1);
        $this->assertNotNull($salle2);

        // Envoyer la requête POST /api/add-film
        $this->client->request(
            'POST',
            '/api/add-film',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($filmData)
        );

        // Vérifier que la réponse a le statut 201 CREATED
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // Vérifier le contenu de la réponse
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Film et séances ajoutés avec succès', $response['success']);

        // Vérifier que le film existe dans la base de données
        $film = $this->entityManager->getRepository(Film::class)->findOneBy(['title' => 'Film de Test']);
        $this->assertNotNull($film);
        $this->assertEquals('Description du film de test.', $film->getDescription());

        // Vérifier les séances associées
        $seances = $film->getSeances();
        $this->assertCount(2, $seances);
    }

    public function testAddFilmForbidden(): void
    {
        // Créer un client non admin
        $clientUser = static::createClient();

        // Créer ou récupérer un utilisateur régulier
        $regularUser = $this->entityManager->getRepository(Utilisateur::class)
            ->findOneBy(['login' => 'user@test.com']);

        if (!$regularUser) {
            $regularUser = new Utilisateur();
            $regularUser->setLogin('user@test.com');
            $regularUser->setPrenom('Regular');
            $regularUser->setNom('User');
            $regularUser->setRoles(['ROLE_USER']);
            $regularUser->setPassword(
                $this->passwordHasher->hashPassword($regularUser, 'userpass')
            );

            $this->entityManager->persist($regularUser);
            $this->entityManager->flush();
        }

        // Authentifier l'utilisateur régulier
        $clientUser->loginUser($regularUser);

        // Préparer les données du film
        $filmData = [
            'title' => 'Film Interdit',
            'description' => 'Description du film interdit.',
            'releaseDate' => '2025-02-01',
            'minimumAge' => 18,
            'note' => 9,
            'affiche' => 'https://example.com/poster2.jpg',
            'genres' => [1], // Assurez-vous que ce genre existe
            'seances' => [],
        ];

        // Envoyer la requête POST /api/add-film avec le client non admin
        $clientUser->request(
            'POST',
            '/api/add-film',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($filmData)
        );

        // Vérifier que la réponse a le statut 403 FORBIDDEN
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        // Vérifier le contenu de la réponse
        $response = json_decode($clientUser->getResponse()->getContent(), true);
        $this->assertEquals('Accès interdit, vous devez être administrateur', $response['error']);
    }

    public function testAddFilmBadRequest(): void
    {
        // Préparer des données incomplètes (manque 'title')
        $filmData = [
            // 'title' => 'Film Incomplet', // Titre manquant
            'description' => 'Description sans titre.',
            'releaseDate' => '2025-03-01',
            'minimumAge' => 10,
            'note' => 7,
            'affiche' => 'https://example.com/poster3.jpg',
            'genres' => [1], // Assurez-vous que ce genre existe
            'seances' => [],
        ];

        // Envoyer la requête POST /api/add-film
        $this->client->request(
            'POST',
            '/api/add-film',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($filmData)
        );

        // Vérifier que la réponse a le statut 400 BAD REQUEST
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        // Vérifier le contenu de la réponse
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Données du film incomplètes.', $response['error']);
    }

    public function testEditFilmWithSeancesSuccess(): void
    {
        // Créer un film de test
        $film = new Film();
        $film->setTitle('Film à Éditer');
        $film->setDescription('Description initiale.');
        $film->setReleaseDate(new \DateTime('2025-05-01'));
        $film->setMinimumAge(16);
        $film->setNote(7);
        $film->setAffiche('https://example.com/poster5.jpg');

        // Créer un genre
        $genre = new Genre();
        $genre->setName('Drame');
        $this->entityManager->persist($genre);
        $film->addGenre($genre);

        // Créer une qualité
        $qualite = new Qualite();
        $qualite->setName('4K');
        $qualite->setPrix(20);
        $this->entityManager->persist($qualite);

        // Créer une salle
        $salle = new Salle();
        $salle->setNumero(3);
        $salle->setCapaciteTotale(150);
        $salle->setCapacitePMR(10);
        $salle->setQualite($qualite);
        $this->entityManager->persist($salle);

        // Créer une séance
        $seance = new Seance();
        $seance->setDateDebut(new \DateTime('2025-06-10T18:00'));
        $seance->setDateFin(new \DateTime('2025-06-10T20:00'));
        $seance->setSalle($salle);
        $seance->setQualite($qualite);
        $seance->setFilm($film);
        $this->entityManager->persist($seance);

        // Persister le film
        $this->entityManager->persist($film);
        $this->entityManager->flush();

        // Préparer les données mises à jour du film
        $updatedFilmData = [
            'title' => 'Film Édité',
            'description' => 'Description mise à jour.',
            'releaseDate' => '2025-06-01',
            'minimumAge' => 18,
            'note' => 9,
            'affiche' => 'https://example.com/poster_updated.jpg',
            'seances' => [
                [
                    'id' => $seance->getId(),
                    'dateDebut' => '2025-06-10T18:00',
                    'dateFin' => '2025-06-10T20:00',
                    'salleId' => $salle->getId(),
                    'qualiteId' => $qualite->getId(),
                ],
            ],
        ];

        // Envoyer la requête PUT /api/admin/edit-film-with-seances/{id}
        $this->client->request(
            'PUT',
            '/api/admin/edit-film-with-seances/' . $film->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($updatedFilmData)
        );

        // Vérifier que la réponse a le statut 200 OK
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Vérifier le contenu de la réponse
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Film et séances modifiés avec succès', $response['success']);
        $this->assertEquals('Film Édité', $response['film']['title']);
        $this->assertEquals('Description mise à jour.', $response['film']['description']);
        $this->assertEquals('2025-06-01', $response['film']['releaseDate']);
        $this->assertEquals(18, $response['film']['minimumAge']);
        $this->assertEquals(9, $response['film']['note']);
        $this->assertEquals('https://example.com/poster_updated.jpg', $response['film']['affiche']);
        $this->assertCount(1, $response['seances']);
        $this->assertEquals('2025-06-10T18:00', $response['seances'][0]['dateDebut']);
        $this->assertEquals('2025-06-10T20:00', $response['seances'][0]['dateFin']);
    }

    public function testDeleteFilmSuccess(): void
    {
        // Créer un film de test
        $film = new Film();
        $film->setTitle('Film à Supprimer');
        $film->setDescription('Description du film à supprimer.');
        $film->setReleaseDate(new \DateTime('2025-07-01'));
        $film->setMinimumAge(10);
        $film->setNote(5);
        $film->setAffiche('https://example.com/poster_delete.jpg');

        // Créer une qualité
        $qualite = new Qualite();
        $qualite->setName('HD');
        $qualite->setPrix(10);
        $this->entityManager->persist($qualite);

        // Créer une salle
        $salle = new Salle();
        $salle->setNumero(4);
        $salle->setCapaciteTotale(100);
        $salle->setCapacitePMR(5);
        $salle->setQualite($qualite);
        $this->entityManager->persist($salle);

        // Créer une séance
        $seance = new Seance();
        $seance->setDateDebut(new \DateTime('2025-07-10T18:00'));
        $seance->setDateFin(new \DateTime('2025-07-10T20:00'));
        $seance->setSalle($salle);
        $seance->setQualite($qualite);
        $seance->setFilm($film);
        $this->entityManager->persist($seance);

        // Persister le film
        $this->entityManager->persist($film);
        $this->entityManager->flush();

        // Envoyer la requête DELETE /api/admin/delete-film/{id}
        $this->client->request(
            'DELETE',
            '/api/admin/delete-film/' . $film->getId()
        );

        // Vérifier que la réponse a le statut 200 OK
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Vérifier le contenu de la réponse
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Film et ses séances supprimés avec succès', $response['success']);

        // Vérifier que le film n'existe plus dans la base
        $deletedFilm = $this->entityManager->getRepository(Film::class)->find($film->getId());
        $this->assertNull($deletedFilm);

        // Vérifier que la séance a également été supprimée
        $deletedSeance = $this->entityManager->getRepository(Seance::class)->find($seance->getId());
        $this->assertNull($deletedSeance);
    }
}
