<?php

namespace App\Tests\Fonctionnel;

use App\Entity\Cinema;
use App\Entity\Film;
use App\Entity\Genre;
use App\Entity\Seance;
use App\Entity\Salle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FilmFunctionalTest extends WebTestCase
{
    private $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);

        // (Optionnel) $this->cleanDatabase();
    }

    /**
     * Test 1 : Sans aucun filtre => renvoie tous les films, genres, cinémas
     */
    public function testGetFilmsNoFilters(): void
    {
        // (1) Créer un cinéma
        $cinema = new Cinema();
        $cinema->setNom('Cinéma Test NF');
        $cinema->setAdresse('1 rue du Test NF');
        $this->entityManager->persist($cinema);

        // (2) Créer un genre
        $genre = new Genre();
        $genre->setName('Action NF');
        $this->entityManager->persist($genre);

        // (3) Créer un film
        $film = new Film();
        $film->setTitle('Film Test NF')
             ->setDescription('Description test NF')
             ->setReleaseDate(new \DateTime('2024-12-01'))
             ->setMinimumAge(12)
             ->setNote(5)
             ->setAffiche('https://example.com/poster_nf.jpg')
             ->addGenre($genre);
        $this->entityManager->persist($film);

        // (4) Créer une salle avec `capaciteTotale` et `capacitePmr`
        $salle = new Salle();
        $salle->setNumero(1);
        $salle->setCapaciteTotale(100);
        $salle->setCapacitePmr(10);      // <-- IMPORTANT : éviter que ça soit NULL
        $salle->setCinema($cinema);
        $this->entityManager->persist($salle);

        // (5) Créer une séance liée au film + salle
        $seance = new Seance();
        $seance->setFilms($film)
               ->setDateDebut(new \DateTime('2024-12-10 10:00'))
               ->setDateFin(new \DateTime('2024-12-10 12:00'))
               ->setSalle($salle);
        $this->entityManager->persist($seance);

        $this->entityManager->flush();

        // (6) Appeler la route SANS filtres
        $this->client->request('GET', '/api/films');

        // (7) Vérifier la réponse (2xx)
        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        // (8) Vérifier la structure => "films", "genres", "cinemas"
        $this->assertArrayHasKey('films', $data);
        $this->assertArrayHasKey('genres', $data);
        $this->assertArrayHasKey('cinemas', $data);

        // (9) Vérifier qu’on a au moins 1 film, 1 genre, 1 cinema
        $this->assertNotEmpty($data['films'],   'Aucun film trouvé alors qu’un film a été créé');
        $this->assertNotEmpty($data['genres'],  'Aucun genre trouvé alors qu’un genre a été créé');
        $this->assertNotEmpty($data['cinemas'], 'Aucun cinéma trouvé alors qu’un cinéma a été créé');
    }

    /**
     * Test 2 : Filtrer par cinemaId
     */
    public function testGetFilmsFilterCinemaId(): void
    {
        // (1) Créer un cinéma
        $cinema = new Cinema();
        $cinema->setNom('Cinéma FilterCID');
        $cinema->setAdresse('2 rue du Test CID');
        $this->entityManager->persist($cinema);

        // (2) Créer un genre
        $genre = new Genre();
        $genre->setName('Action CID');
        $this->entityManager->persist($genre);

        // (3) Créer un film
        $film = new Film();
        $film->setTitle('Film FilterCID')
             ->setDescription('Description FilterCID')
             ->setReleaseDate(new \DateTime('2025-01-01'))
             ->setMinimumAge(12)
             ->setNote(4)
             ->addGenre($genre);
        $this->entityManager->persist($film);

        // (4) Créer une salle
        $salle = new Salle();
        $salle->setNumero(7);
        $salle->setCapaciteTotale(80);
        $salle->setCapacitePmr(5);   // <-- IMPORTANT
        $salle->setCinema($cinema);
        $this->entityManager->persist($salle);

        // (5) Créer une séance
        $seance = new Seance();
        $seance->setFilms($film)
               ->setDateDebut(new \DateTime('2025-01-10 15:00'))
               ->setDateFin(new \DateTime('2025-01-10 17:00'))
               ->setSalle($salle);
        $this->entityManager->persist($seance);

        $this->entityManager->flush();

        // (6) /api/films?cinemaId=...
        $cinemaId = $cinema->getId();
        $this->client->request('GET', '/api/films?cinemaId=' . $cinemaId);

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('films', $data, 'La clé "films" doit exister.');
        $this->assertNotEmpty($data['films'],   'On attend au moins 1 film pour ce cinemaId');
    }

    /**
     * Test 3 : Filtrer par genre
     */
    public function testGetFilmsFilterGenre(): void
    {
        // 1) Créer un genre 'ComedyFG'
        $genre = new Genre();
        $genre->setName('ComedyFG');
        $this->entityManager->persist($genre);

        // 2) Créer un film
        $film = new Film();
        $film->setTitle('Film Genre FG')
             ->setDescription('Desc FG')
             ->setReleaseDate(new \DateTime('2025-02-01'))
             ->setMinimumAge(12)
             ->setNote(3)
             ->addGenre($genre);
        $this->entityManager->persist($film);

        // 3) Créer un cinéma + salle
        $cinema = new Cinema();
        $cinema->setNom('CinemaFG');
        $cinema->setAdresse('Rue FG');
        $this->entityManager->persist($cinema);

        $salle = new Salle();
        $salle->setNumero(8);
        $salle->setCapaciteTotale(120);
        $salle->setCapacitePmr(6);   // <-- IMPORTANT
        $salle->setCinema($cinema);
        $this->entityManager->persist($salle);

        // 4) Créer une séance
        $seance = new Seance();
        $seance->setFilms($film)
               ->setDateDebut(new \DateTime('2025-02-10 14:00'))
               ->setDateFin(new \DateTime('2025-02-10 16:00'))
               ->setSalle($salle);
        $this->entityManager->persist($seance);

        $this->entityManager->flush();

        // 5) /api/films?genre=ComedyFG
        $this->client->request('GET', '/api/films?genre=ComedyFG');

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('films', $data);
        $this->assertNotEmpty($data['films'], 'Aucun film trouvé alors qu’on en a un avec genre=ComedyFG');
    }

    /**
     * Test 4 : Filtrer par date
     */
    public function testGetFilmsFilterDate(): void
    {
        // 1) Créer un film
        $film = new Film();
        $film->setTitle('Film FilterDate')
             ->setDescription('Desc FD')
             ->setReleaseDate(new \DateTime('2025-03-01'))
             ->setMinimumAge(12)
             ->setNote(4);
        $this->entityManager->persist($film);

        // 2) Créer un cinéma + salle
        $cinema = new Cinema();
        $cinema->setNom('CinemaDate');
        $cinema->setAdresse('Rue Date');
        $this->entityManager->persist($cinema);

        $salle = new Salle();
        $salle->setNumero(9);
        $salle->setCapaciteTotale(200);
        $salle->setCapacitePmr(15);  // <-- IMPORTANT
        $salle->setCinema($cinema);
        $this->entityManager->persist($salle);

        // 3) Créer une seance le 2025-03-15
        $seance = new Seance();
        $seance->setFilms($film)
               ->setDateDebut(new \DateTime('2025-03-15 10:00'))
               ->setDateFin(new \DateTime('2025-03-15 12:00'))
               ->setSalle($salle);
        $this->entityManager->persist($seance);

        $this->entityManager->flush();

        // 4) /api/films?date=2025-03-15
        $this->client->request('GET', '/api/films?date=2025-03-15');

        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('films', $data);
        $this->assertNotEmpty($data['films'], 'Il devrait y avoir au moins un film ce jour-là');
    }

    /**
     * Test 5 : Combinaison de filtres (cinemaId + genre + date)
     */
    public function testGetFilmsWithAllFilters(): void
    {
        // 1) Créer un cinema
        $cinema = new Cinema();
        $cinema->setNom('CineAllFilters');
        $cinema->setAdresse('Route AF');
        $this->entityManager->persist($cinema);

        // 2) Créer un genre
        $genre = new Genre();
        $genre->setName('GenreAF');
        $this->entityManager->persist($genre);

        // 3) Créer un film
        $film = new Film();
        $film->setTitle('Film AF')
             ->setDescription('Desc AF')
             ->setReleaseDate(new \DateTime('2025-04-01'))
             ->setMinimumAge(12)
             ->setNote(5)
             ->addGenre($genre);
        $this->entityManager->persist($film);

        // 4) Créer une salle
        $salle = new Salle();
        $salle->setNumero(10);
        $salle->setCapaciteTotale(150);
        $salle->setCapacitePmr(10);  // <-- IMPORTANT
        $salle->setCinema($cinema);
        $this->entityManager->persist($salle);

        // 5) Créer une seance date=2025-04-01
        $seance = new Seance();
        $seance->setFilms($film)
               ->setDateDebut(new \DateTime('2025-04-01 10:00'))
               ->setDateFin(new \DateTime('2025-04-01 12:00'))
               ->setSalle($salle);
        $this->entityManager->persist($seance);

        $this->entityManager->flush();

        // 6) /api/films?cinemaId=X&genre=GenreAF&date=2025-04-01
        $cinemaId = $cinema->getId();
        $genreName = 'GenreAF';
        $date = '2025-04-01';

        $url = "/api/films?cinemaId={$cinemaId}&genre={$genreName}&date={$date}";
        $this->client->request('GET', $url);

        // 7) Vérifier
        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('films', $data);
        $this->assertNotEmpty($data['films'], 'On s’attend à au moins 1 film avec tous les filtres');
    }

    private function cleanDatabase(): void
    {
        // ...
    }
}
