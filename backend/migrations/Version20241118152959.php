<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241118152959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE film (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, release_date DATETIME NOT NULL, minimum_age INT NOT NULL, note DOUBLE PRECISION NOT NULL, is_favorite TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE film_genre (film_id INT NOT NULL, genre_id INT NOT NULL, INDEX IDX_1A3CCDA8567F5183 (film_id), INDEX IDX_1A3CCDA84296D31F (genre_id), PRIMARY KEY(film_id, genre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE incident (id INT AUTO_INCREMENT NOT NULL, salle_id INT DEFAULT NULL, description LONGTEXT NOT NULL, date_signalement DATETIME NOT NULL, resolu TINYINT(1) NOT NULL, INDEX IDX_3D03A11ADC304035 (salle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE qualite (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE qualite_film (qualite_id INT NOT NULL, film_id INT NOT NULL, INDEX IDX_59C400DEA6338570 (qualite_id), INDEX IDX_59C400DE567F5183 (film_id), PRIMARY KEY(qualite_id, film_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, utilisateur_id INT DEFAULT NULL, seances_id INT DEFAULT NULL, nombre_sieges INT NOT NULL, sieges_reserves LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', prix_total DOUBLE PRECISION NOT NULL, INDEX IDX_42C84955FB88E14F (utilisateur_id), INDEX IDX_42C8495510F09302 (seances_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE salle (id INT AUTO_INCREMENT NOT NULL, qualite_id INT DEFAULT NULL, numero INT NOT NULL, capacite_totale INT NOT NULL, capacite_pmr INT NOT NULL, INDEX IDX_4E977E5CA6338570 (qualite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seance (id INT AUTO_INCREMENT NOT NULL, salle_id INT DEFAULT NULL, films_id INT DEFAULT NULL, salles_id INT DEFAULT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, INDEX IDX_DF7DFD0EDC304035 (salle_id), INDEX IDX_DF7DFD0E939610EE (films_id), INDEX IDX_DF7DFD0EB11E4946 (salles_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, login VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE film_genre ADD CONSTRAINT FK_1A3CCDA8567F5183 FOREIGN KEY (film_id) REFERENCES film (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE film_genre ADD CONSTRAINT FK_1A3CCDA84296D31F FOREIGN KEY (genre_id) REFERENCES genre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE incident ADD CONSTRAINT FK_3D03A11ADC304035 FOREIGN KEY (salle_id) REFERENCES salle (id)');
        $this->addSql('ALTER TABLE qualite_film ADD CONSTRAINT FK_59C400DEA6338570 FOREIGN KEY (qualite_id) REFERENCES qualite (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE qualite_film ADD CONSTRAINT FK_59C400DE567F5183 FOREIGN KEY (film_id) REFERENCES film (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495510F09302 FOREIGN KEY (seances_id) REFERENCES seance (id)');
        $this->addSql('ALTER TABLE salle ADD CONSTRAINT FK_4E977E5CA6338570 FOREIGN KEY (qualite_id) REFERENCES qualite (id)');
        $this->addSql('ALTER TABLE seance ADD CONSTRAINT FK_DF7DFD0EDC304035 FOREIGN KEY (salle_id) REFERENCES salle (id)');
        $this->addSql('ALTER TABLE seance ADD CONSTRAINT FK_DF7DFD0E939610EE FOREIGN KEY (films_id) REFERENCES film (id)');
        $this->addSql('ALTER TABLE seance ADD CONSTRAINT FK_DF7DFD0EB11E4946 FOREIGN KEY (salles_id) REFERENCES salle (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE film_genre DROP FOREIGN KEY FK_1A3CCDA8567F5183');
        $this->addSql('ALTER TABLE film_genre DROP FOREIGN KEY FK_1A3CCDA84296D31F');
        $this->addSql('ALTER TABLE incident DROP FOREIGN KEY FK_3D03A11ADC304035');
        $this->addSql('ALTER TABLE qualite_film DROP FOREIGN KEY FK_59C400DEA6338570');
        $this->addSql('ALTER TABLE qualite_film DROP FOREIGN KEY FK_59C400DE567F5183');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955FB88E14F');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495510F09302');
        $this->addSql('ALTER TABLE salle DROP FOREIGN KEY FK_4E977E5CA6338570');
        $this->addSql('ALTER TABLE seance DROP FOREIGN KEY FK_DF7DFD0EDC304035');
        $this->addSql('ALTER TABLE seance DROP FOREIGN KEY FK_DF7DFD0E939610EE');
        $this->addSql('ALTER TABLE seance DROP FOREIGN KEY FK_DF7DFD0EB11E4946');
        $this->addSql('DROP TABLE film');
        $this->addSql('DROP TABLE film_genre');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE incident');
        $this->addSql('DROP TABLE qualite');
        $this->addSql('DROP TABLE qualite_film');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE salle');
        $this->addSql('DROP TABLE seance');
        $this->addSql('DROP TABLE utilisateur');
    }
}
