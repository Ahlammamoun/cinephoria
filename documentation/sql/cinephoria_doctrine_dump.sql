-- MySQL dump 10.13  Distrib 8.0.40, for Linux (x86_64)
--
-- Host: localhost    Database: cinephoria
-- ------------------------------------------------------
-- Server version	8.0.40

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cinema`
--

DROP TABLE IF EXISTS `cinema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cinema` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cinema`
--

LOCK TABLES `cinema` WRITE;
/*!40000 ALTER TABLE `cinema` DISABLE KEYS */;
INSERT INTO `cinema` VALUES (1,'Megaramos','rue des steps'),(2,'Megaramos','rue des anges etbelzebuth');
/*!40000 ALTER TABLE `cinema` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_request`
--

DROP TABLE IF EXISTS `contact_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_request` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_request`
--

LOCK TABLES `contact_request` WRITE;
/*!40000 ALTER TABLE `contact_request` DISABLE KEYS */;
INSERT INTO `contact_request` VALUES (1,'bill','ça pu ','frfefaerg','2025-01-01 16:50:46'),(2,'test','test','test','2025-01-19 14:45:11');
/*!40000 ALTER TABLE `contact_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctrine_migration_versions`
--

LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20241210191245','2024-12-10 19:14:00',30),('DoctrineMigrations\\Version20241211122940','2024-12-11 12:29:48',20),('DoctrineMigrations\\Version20241211123607','2024-12-11 12:36:14',105),('DoctrineMigrations\\Version20241223142619','2024-12-25 18:30:53',80),('DoctrineMigrations\\Version20241226130821','2024-12-26 13:08:28',93),('DoctrineMigrations\\Version20241226133208','2024-12-26 13:32:15',34),('DoctrineMigrations\\Version20241226171001','2024-12-26 17:10:09',84),('DoctrineMigrations\\Version20241230162356','2024-12-30 16:25:30',45),('DoctrineMigrations\\Version20250101164837','2025-01-01 16:49:12',46);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `film`
--

DROP TABLE IF EXISTS `film`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `film` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `release_date` datetime NOT NULL,
  `minimum_age` int NOT NULL,
  `note` double NOT NULL,
  `is_favorite` tinyint(1) DEFAULT NULL,
  `affiche` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `film`
--

LOCK TABLES `film` WRITE;
/*!40000 ALTER TABLE `film` DISABLE KEYS */;
INSERT INTO `film` VALUES (1,'The Inception','A skilled thief, the absolute best in the dangerous art of extraction, stealing valuable secrets from deep within the subconscious during the dream state.','2025-01-22 00:00:00',13,6.4,1,'image1.jpeg'),(2,'Interstellar Galac','A team of explorers travel through a wormhole in space in an attempt to ensure humanity\'s survival.','2025-01-01 00:00:00',12,6.8,1,'image2.jpeg'),(3,'The Matrix 25','A computer hacker learns from mysterious rebels about the true nature of his reality and his role in the war against its controllers.','2025-01-22 00:00:00',16,5,0,'image3.jpeg'),(4,'The Wandering ','A lone astronaut ventures into the unknown depths of the universe, seeking answers to humanity\'s most profound questions.','2025-01-22 00:00:00',10,8.4,1,'image4.jpeg'),(5,' Symphony','A gifted pianist discovers a secret composition that holds the key to an ancient mystery.','2025-01-22 00:00:00',12,7.9,0,'image5.jpeg'),(6,'Echoes Forest','An anthropologist stumbles upon an enchanted forest where whispers of ancient spirits reveal the secrets of the past.','2025-01-01 00:00:00',14,5.55,1,'image6.jpeg'),(7,'The Horizon','A courageous sailor navigates uncharted waters in search of a legendary treasure, testing his resolve and humanity.','2025-01-22 00:00:00',10,7.5,0,'image7.jpeg'),(8,'Shadows','In a small town plagued by disappearances, a detective uncovers a dark secret hidden in the shadows.','2025-01-15 00:00:00',16,8.9,1,'image8.jpeg'),(9,'sharks big white','In a small town plagued by disappearances, a detective uncovers a dark secret hidden in the shadows.','2025-01-22 00:00:00',18,8.9,1,'image9.jpeg'),(10,'bears akam','In a small town plagued by disappearances, a detective uncovers a dark secret hidden in the shadows.','2025-01-15 00:00:00',16,8.9,1,'image10.jpeg'),(11,'the foot','In a small town plagued by disappearances, a detective uncovers a dark secret hidden in the shadows.','2025-01-22 00:00:00',18,8.9,1,'image11.jpeg'),(12,'billoute','A skilled thief, the absolute best in the dangerous art of extraction, stealing valuable secrets from deep within the subconscious during the dream state.','2024-10-16 00:00:00',13,8.8,1,'image12.jpeg'),(13,'au bout de monde','Small Country: An African Childhood (French: Petit Pays) is a 2020 film written and directed by Éric Barbier. It is a co-production between France and','2025-01-22 00:00:00',18,3.8,NULL,'image13.jpeg'),(14,'Arnold et Billy','An image is a visual representation. An image can be two-dimensional, such as a drawing, painting, or photograph, or three-dimensional, such as a carving or ...','2025-01-01 00:00:00',5,7,NULL,'image14.jpeg'),(15,'Les ombres','Définitions : oriental, orientale - Dictionnaire de français ...\n\nLarousse\nhttps://www.larousse.fr › francais\n·\nTranslate this page\nQui est situé à l\'est : La côte orientale de la Corse. 2. Relatif à l\'Orient : Peuples orientaux. Contraire : occidental. 3. Qui est propre à l\'Orient, ..','2025-01-22 00:00:00',4,4,NULL,'image15.jpeg'),(16,'voyage ','n adventure is an exciting experience or undertaking that is typically bold, sometimes risky.[1] Adventures may be activities with danger such as traveling, exploring, skydiving, mountain climbing, scuba diving, river rafting, or othe','2025-01-15 00:00:00',9,4,NULL,'image16.jpeg'),(17,'Bobbie lV le retour','La boxe est un sport complet dont les bienfaits se ressentent aussi bien physiquement que mentalement. En pratiquant cette discipline, vous allez perdre du poids et tonifier l\'ensemble des muscles de votre corps. Vous allez aussi apprendre à mieux maîtriser votre souffle, votre cardio et vos émotions.Dec 29, 2021','2025-01-01 00:00:00',15,5,NULL,'image17.jpeg'),(18,'Planète rouge','1. Propriété particulière d\'un objet qui fait que celui-ci occupe une certaine étendue, un certain volume au sein d\'une étendue, d\'un volume nécessairement plus grands que lui et qui peuvent être mesurés. 2. Étendue, surface ou volume dont o','2025-01-22 00:00:00',14,5,NULL,'image18.jpeg'),(20,'A la limite de la mort','En effet, l\'année 2024 a été marquée par le gigantesque succès surprise de la comédie d\'Artus, Un P\'tit truc en plus qui termine en tête du classement en atteignant la barre symbolique des 10 millions d\'entrées.1 day ago','2025-01-22 00:00:00',4,14,NULL,'image19.jpeg'),(21,'Next day','Découvrez plus de 5.2 million d\'images gratuites et de haute qualité partagées par notre talentueuse communauté.','2024-12-22 00:00:00',12,12,NULL,'image20.jpeg'),(22,'La limite ','Retrouvez tous les films au cinéma, les séances dans toute la France, le guide des meilleurs films à voir, les films à voir à la TV, ','2025-01-22 00:00:00',9,5,NULL,'image21.jpeg');
/*!40000 ALTER TABLE `film` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `film_genre`
--

DROP TABLE IF EXISTS `film_genre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `film_genre` (
  `film_id` int NOT NULL,
  `genre_id` int NOT NULL,
  PRIMARY KEY (`film_id`,`genre_id`),
  KEY `IDX_1A3CCDA8567F5183` (`film_id`),
  KEY `IDX_1A3CCDA84296D31F` (`genre_id`),
  CONSTRAINT `FK_1A3CCDA84296D31F` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_1A3CCDA8567F5183` FOREIGN KEY (`film_id`) REFERENCES `film` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `film_genre`
--

LOCK TABLES `film_genre` WRITE;
/*!40000 ALTER TABLE `film_genre` DISABLE KEYS */;
INSERT INTO `film_genre` VALUES (1,1),(2,4),(3,4),(4,5),(5,2),(5,3),(6,1),(7,4),(8,2),(9,1),(10,3),(11,3),(12,6),(13,1),(14,2),(15,3),(16,6),(17,7),(18,7),(22,1);
/*!40000 ALTER TABLE `film_genre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `genre`
--

DROP TABLE IF EXISTS `genre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `genre` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `genre`
--

LOCK TABLES `genre` WRITE;
/*!40000 ALTER TABLE `genre` DISABLE KEYS */;
INSERT INTO `genre` VALUES (1,'Action'),(2,'Drama'),(3,'Comedy'),(4,'Science Fiction'),(5,'Horror'),(6,'Thriller'),(7,'Romance');
/*!40000 ALTER TABLE `genre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `incident`
--

DROP TABLE IF EXISTS `incident`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `incident` (
  `id` int NOT NULL AUTO_INCREMENT,
  `salle_id` int DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_signalement` datetime NOT NULL,
  `resolu` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3D03A11ADC304035` (`salle_id`),
  CONSTRAINT `FK_3D03A11ADC304035` FOREIGN KEY (`salle_id`) REFERENCES `salle` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `incident`
--

LOCK TABLES `incident` WRITE;
/*!40000 ALTER TABLE `incident` DISABLE KEYS */;
/*!40000 ALTER TABLE `incident` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messenger_messages`
--

LOCK TABLES `messenger_messages` WRITE;
/*!40000 ALTER TABLE `messenger_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messenger_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qualite`
--

DROP TABLE IF EXISTS `qualite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qualite` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prix` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qualite`
--

LOCK TABLES `qualite` WRITE;
/*!40000 ALTER TABLE `qualite` DISABLE KEYS */;
INSERT INTO `qualite` VALUES (1,'4DX',0),(2,'3D',0),(3,'IMAX',0),(4,'Standard',0),(5,'4K',0);
/*!40000 ALTER TABLE `qualite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qualite_film`
--

DROP TABLE IF EXISTS `qualite_film`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qualite_film` (
  `qualite_id` int NOT NULL,
  `film_id` int NOT NULL,
  PRIMARY KEY (`qualite_id`,`film_id`),
  KEY `IDX_59C400DEA6338570` (`qualite_id`),
  KEY `IDX_59C400DE567F5183` (`film_id`),
  CONSTRAINT `FK_59C400DE567F5183` FOREIGN KEY (`film_id`) REFERENCES `film` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_59C400DEA6338570` FOREIGN KEY (`qualite_id`) REFERENCES `qualite` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qualite_film`
--

LOCK TABLES `qualite_film` WRITE;
/*!40000 ALTER TABLE `qualite_film` DISABLE KEYS */;
/*!40000 ALTER TABLE `qualite_film` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservation`
--

DROP TABLE IF EXISTS `reservation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int DEFAULT NULL,
  `seances_id` int DEFAULT NULL,
  `nombre_sieges` int NOT NULL,
  `sieges_reserves` json DEFAULT NULL,
  `prix_total` double NOT NULL,
  `date_reservation` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_42C84955FB88E14F` (`utilisateur_id`),
  KEY `IDX_42C8495510F09302` (`seances_id`),
  CONSTRAINT `FK_42C8495510F09302` FOREIGN KEY (`seances_id`) REFERENCES `seance` (`id`),
  CONSTRAINT `FK_42C84955FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservation`
--

LOCK TABLES `reservation` WRITE;
/*!40000 ALTER TABLE `reservation` DISABLE KEYS */;
INSERT INTO `reservation` VALUES (1,25,1,2,'[\"A1\", \"A2\"]',25,'2024-12-25 18:39:51'),(2,25,2,3,'[\"B1\", \"B2\", \"B3\"]',37.5,'2024-12-25 18:39:51'),(3,25,2,3,'[\"C1\"]',37.5,'2024-12-25 18:39:51'),(4,3,3,1,'[\"D1\", \"D2\", \"D3\", \"D4\"]',12.5,'2024-12-25 18:39:51'),(5,4,4,4,'[\"E1\", \"E2\"]',50,'2024-12-25 18:39:51'),(6,5,5,2,'[\"E1\", \"E2\"]',30,'2024-12-25 18:39:51'),(7,NULL,1,2,'[\"S1\", \"S2\"]',0,'2024-12-25 18:39:51'),(8,NULL,1,2,'[\"S1\", \"S2\"]',0,'2024-12-25 18:39:51'),(9,NULL,1,1,'[\"S1\"]',0,'2024-12-25 18:39:51'),(10,NULL,1,1,'[\"S1\"]',0,'2024-12-25 18:39:51'),(11,NULL,3,6,'[\"S1\", \"S2\", \"S3\", \"S4\", \"S5\", \"S6\"]',180,'2024-12-25 18:39:51'),(12,NULL,1,1,'[\"S1\"]',22,'2024-12-25 18:39:51'),(13,NULL,1,4,'[\"S1\", \"S2\", \"S3\", \"S4\"]',88,'2024-12-25 18:39:51'),(14,NULL,1,3,'[\"S85\", \"S86\", \"S87\"]',66,'2024-12-25 18:39:51'),(15,NULL,1,5,'[\"S21\", \"S7\", \"S15\", \"S14\", \"S12\"]',110,'2024-12-25 18:39:51'),(16,NULL,1,5,'[\"S15\", \"S22\", \"S21\", \"S4\", \"S12\"]',110,'2024-12-25 18:39:51'),(17,NULL,1,4,'[\"S14\", \"S13\", \"S12\", \"S11\"]',88,'2024-12-25 18:39:51'),(18,NULL,1,3,'[\"S6\", \"S5\", \"S4\"]',66,'2024-12-25 18:39:51'),(19,NULL,1,3,'[\"S6\", \"S5\", \"S4\"]',66,'2024-12-25 18:39:51'),(20,NULL,1,3,'[\"S6\", \"S5\", \"S4\"]',66,'2024-12-25 18:39:51'),(21,NULL,1,4,'[\"S23\", \"S22\", \"S14\", \"S15\"]',88,'2024-12-25 18:39:51'),(22,NULL,3,5,'[\"S54\", \"S116\", \"S110\", \"S101\", \"S99\"]',150,'2024-12-25 18:39:51'),(23,NULL,3,5,'[\"S54\", \"S116\", \"S110\", \"S101\", \"S99\"]',150,'2024-12-25 18:39:51'),(24,NULL,3,2,'[\"S54\", \"S116\"]',60,'2024-12-25 18:39:51'),(25,NULL,3,3,'[\"S9\", \"S10\", \"S8\"]',90,'2024-12-25 18:39:51'),(26,NULL,3,3,'[\"S9\", \"S10\", \"S8\"]',90,'2024-12-25 18:39:51'),(27,NULL,3,4,'[\"S7\", \"S11\", \"S12\", \"S13\"]',120,'2024-12-25 18:39:51'),(28,NULL,3,13,'[\"S68\", \"S15\", \"S17\", \"S19\", \"S21\", \"S23\", \"S25\", \"S27\", \"S44\", \"S41\", \"S40\", \"S39\", \"S38\"]',390,'2024-12-25 18:39:51'),(29,NULL,3,1,'[\"S67\"]',30,'2024-12-25 18:39:51'),(30,NULL,3,4,'[\"S14\", \"S16\", \"S24\", \"S28\"]',150,'2024-12-25 18:39:51'),(31,NULL,3,5,'[\"S22\", \"S30\", \"S31\", \"S32\", \"S33\"]',150,'2024-12-25 18:39:51'),(32,NULL,3,2,'[\"S43\", \"S42\"]',60,'2024-12-25 18:39:51'),(33,NULL,3,1,'[\"S91\"]',30,'2024-12-25 18:39:51'),(34,NULL,3,1,'[\"S60\"]',30,'2024-12-25 18:39:51'),(35,NULL,3,1,'[\"S60\"]',30,'2024-12-25 18:39:51'),(36,NULL,3,1,'[\"S45\"]',30,'2024-12-25 18:39:51'),(37,NULL,5,1,'[\"S10\"]',15,'2024-12-25 18:39:51'),(38,NULL,1,2,'[\"S24\", \"S38\"]',44,'2024-12-25 18:39:51'),(39,25,3,2,'[\"S46\", \"S37\"]',60,'2024-12-25 18:39:51'),(40,25,1,3,'[\"S58\", \"S43\", \"S72\"]',66,'2024-12-25 18:39:51'),(41,1,2,2,'[\"S10\", \"S25\"]',24,'2024-12-25 18:39:51'),(42,25,1,2,'[\"S25\", \"S41\"]',44,'2024-12-25 18:39:51'),(43,25,3,3,'[\"S100\", \"S82\", \"S81\"]',90,'2024-12-25 18:39:51'),(44,25,2,1,'[\"S40\"]',12,'2024-12-25 18:39:51'),(45,25,2,1,'[\"S8\"]',12,'2024-12-25 18:39:51'),(46,25,5,2,'[\"S13\", \"S27\"]',30,'2024-12-25 18:39:51'),(47,25,1,2,'[\"S26\", \"S39\"]',44,'2024-12-25 18:39:51'),(48,25,12,3,'[\"S26\", \"S54\", \"S39\"]',90,'2024-12-25 18:39:51'),(49,25,2,1,'[\"S114\"]',12,'2024-12-26 16:18:48'),(50,25,2,3,'[\"S114\", \"S41\", \"S54\"]',36,'2024-12-26 16:19:14'),(51,25,2,4,'[\"S12\", \"S13\", \"S14\", \"S15\"]',48,'2024-12-26 16:20:25'),(52,25,7,3,'[\"S11\", \"S12\", \"S13\"]',66,'2024-12-30 15:40:45'),(53,25,3,2,'[\"S47\", \"S48\"]',60,'2024-12-31 20:00:15'),(54,8,7,3,'[\"S8\", \"S9\", \"S10\"]',66,'2025-01-01 17:05:42'),(55,8,12,5,'[\"S1\", \"S2\", \"S3\", \"S4\", \"S5\"]',150,'2025-01-01 17:06:18'),(56,8,13,5,'[\"S1\", \"S2\", \"S3\", \"S4\", \"S5\"]',150,'2025-01-01 17:06:54'),(57,8,5,4,'[\"S9\", \"S11\", \"S12\", \"S14\"]',60,'2025-01-01 17:07:38'),(58,8,13,4,'[\"S6\", \"S7\", \"S8\", \"S9\"]',120,'2025-01-01 17:10:21'),(59,8,23,7,'[\"S15\", \"S14\", \"S13\", \"S12\", \"S11\", \"S10\", \"S9\"]',210,'2025-01-01 17:46:51'),(60,8,20,7,'[\"S15\", \"S14\", \"S13\", \"S12\", \"S11\", \"S8\", \"S7\"]',154,'2025-01-01 17:47:07'),(61,8,24,6,'[\"S6\", \"S5\", \"S4\", \"S3\", \"S2\", \"S1\"]',84,'2025-01-01 17:51:06'),(62,8,24,2,'[\"S37\", \"S38\"]',36,'2025-01-01 17:52:22'),(63,25,3,3,'[\"S50\", \"S51\", \"S52\"]',90,'2025-01-01 18:33:28'),(64,25,4,3,'[\"S1\", \"S2\", \"S3\"]',30,'2025-01-01 18:34:07'),(65,25,4,2,'[\"S55\", \"S56\"]',20,'2025-01-01 18:34:22'),(66,25,16,4,'[\"S10\", \"S23\", \"S24\", \"S25\"]',48,'2025-01-19 14:44:20');
/*!40000 ALTER TABLE `reservation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salle`
--

DROP TABLE IF EXISTS `salle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `salle` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero` int NOT NULL,
  `capacite_totale` int NOT NULL,
  `capacite_pmr` int NOT NULL,
  `qualite_id` int DEFAULT NULL,
  `cinema_id` int DEFAULT NULL,
  `reparations` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `IDX_4E977E5CA6338570` (`qualite_id`),
  KEY `IDX_4E977E5CB4CB84B6` (`cinema_id`),
  CONSTRAINT `FK_4E977E5CA6338570` FOREIGN KEY (`qualite_id`) REFERENCES `qualite` (`id`),
  CONSTRAINT `FK_4E977E5CB4CB84B6` FOREIGN KEY (`cinema_id`) REFERENCES `cinema` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salle`
--

LOCK TABLES `salle` WRITE;
/*!40000 ALTER TABLE `salle` DISABLE KEYS */;
INSERT INTO `salle` VALUES (1,1,1000,5,2,1,''),(2,2,150,10,2,2,NULL),(3,3,120,8,3,1,NULL),(4,4,200,12,4,2,NULL),(5,5,80,4,5,2,NULL),(8,1,100,10,3,1,'odeur étrange');
/*!40000 ALTER TABLE `salle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seance`
--

DROP TABLE IF EXISTS `seance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `salle_id` int DEFAULT NULL,
  `films_id` int DEFAULT NULL,
  `qualite_id` int DEFAULT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DF7DFD0EDC304035` (`salle_id`),
  KEY `IDX_DF7DFD0E939610EE` (`films_id`),
  KEY `IDX_DF7DFD0EA6338570` (`qualite_id`),
  CONSTRAINT `FK_DF7DFD0E939610EE` FOREIGN KEY (`films_id`) REFERENCES `film` (`id`),
  CONSTRAINT `FK_DF7DFD0EA6338570` FOREIGN KEY (`qualite_id`) REFERENCES `qualite` (`id`),
  CONSTRAINT `FK_DF7DFD0EDC304035` FOREIGN KEY (`salle_id`) REFERENCES `salle` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seance`
--

LOCK TABLES `seance` WRITE;
/*!40000 ALTER TABLE `seance` DISABLE KEYS */;
INSERT INTO `seance` VALUES (1,1,1,1,'2025-12-12 10:00:00','2025-12-12 12:00:00'),(2,2,2,2,'2025-12-12 13:00:00','2025-12-12 23:30:00'),(3,3,3,3,'2025-12-12 18:00:00','2025-12-12 20:00:00'),(4,3,4,4,'2025-12-12 00:00:00','2025-12-12 02:30:00'),(5,5,5,5,'2024-12-12 22:00:00','2024-12-12 23:30:00'),(6,1,6,1,'2024-12-14 10:00:00','2024-12-14 12:00:00'),(7,2,6,1,'2024-12-14 14:00:00','2024-12-14 16:00:00'),(8,3,7,2,'2025-12-15 10:00:00','2025-12-15 12:30:00'),(9,4,7,2,'2024-12-15 17:00:00','2024-12-15 19:30:00'),(10,5,8,1,'2025-12-16 11:00:00','2025-12-16 13:00:00'),(11,1,8,1,'2024-12-16 15:00:00','2024-12-16 17:00:00'),(12,2,9,3,'2025-12-17 09:00:00','2025-12-17 11:30:00'),(13,3,9,3,'2026-12-17 14:00:00','2026-12-17 16:30:00'),(14,4,10,1,'2024-12-18 12:00:00','2024-12-18 14:00:00'),(15,5,10,1,'2024-12-18 16:00:00','2024-12-18 18:00:00'),(16,1,11,2,'2024-12-19 11:00:00','2024-12-19 13:00:00'),(17,2,11,2,'2024-12-19 15:00:00','2024-12-19 17:00:00'),(18,3,12,1,'2024-12-20 10:00:00','2024-12-20 12:00:00'),(19,4,12,1,'2024-12-20 14:00:00','2024-12-20 16:00:00'),(20,3,12,1,'2024-12-20 10:00:00','2024-12-20 12:00:00'),(21,4,12,1,'2024-12-20 14:00:00','2024-12-20 16:00:00'),(22,2,21,2,'2024-12-26 08:00:00','2024-12-26 10:00:00'),(23,3,22,3,'2024-12-31 14:46:00','2024-12-31 18:46:00'),(24,3,16,2,'2024-12-31 14:46:00','2024-12-31 18:46:00');
/*!40000 ALTER TABLE `seance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `utilisateur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `login` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requires_password_change` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1D1C63B3AA08CB10` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateur`
--

LOCK TABLES `utilisateur` WRITE;
/*!40000 ALTER TABLE `utilisateur` DISABLE KEYS */;
INSERT INTO `utilisateur` VALUES (1,'klk','$2y$13$gVWG2igA/IoYLlOoqFZaHejogAdmljPF20aNL6MB3NPWGBH2HfpOi','mlk','lmkmlk','user',0),(2,'rgegrg','$2y$13$qgoFvqJNR6Go7d3bgtkQkOnSr8qZCJEOscvWuPf21Znla0zq8t9lW','erv','erv','user',0),(3,'hyjujuyk','$2y$13$GIixegGXx9OvDXXYfYn0cuv37ksbM2adYr3h2pXbnaET8iXkuSP8W','erv','erv','user',0),(4,'\'\"r\'fdfdsfd','$2y$13$9/32qY0ZubsG3VEW6e6gf.PFQE6JggMvXMSBMfKaEEB0x1sBx5FTO','erv','erv','user',0),(5,'yrh','$2y$13$elKOdvymgYT29vx5vUm.eumNjO1EhL/pHdPAoPS1DIoMJZLl.TmdO','ht','htrh','user',0),(8,'test@gmail.com','$2y$13$OoMkjo97/qNt9YhxZU.RNuv.Tzb7pCiqwJwzH2TX.vlmoTr96nsNW','Isidor','Erwan','admin',1),(17,'allo@gmail.com','$2y$13$ZpPqMwGXQHwbSWzqrIcQsOBpMv1QcRPPvO1q9gOSL619dPq3DaGje','allo','allo','admin',0),(25,'ahlam@gmail.com','$2y$13$FYYajNcjIFxTiMQzTfpACusWWykCKvReVUNtQUOzUqA52aqFkrhCK','Ahlam','Mestar','user',1),(30,'employe','$2y$13$fucmhUPsCjkV3Up2tNYMk.NfYBxakbWbQ.5dVdh1yIu1hfncAhN7y','employe','employe','admin',0),(31,'employe@gmail.com','$2y$13$f3xKjM9ew61A3Ho36xkhuuTOazmr6ru3oEYCB90L6aWc/5ckn4D8K','employe','employe','admin',0),(32,'fesf','$2y$13$5HePqPZXKl2yjaWc4CuddO1BnnNhqUjMXhfyIJkYFNLyoZkXcNTbq','fezf','fezf','employe',0),(33,'efe','$2y$13$7Q198YvFE.EDID39UjEwKOzuUe7r/0z8bP1zwKrdGeiHVtYfjljlS','f','zf','user',0),(34,'mestar.ale@gmail.com','$2y$13$8ZUaHbkfMa9nhe4KNAzWzujJRxlghr4LVCJ8XE666cj78o84FZrYa','ahlam','ahlam','user',1),(35,'mestar.ale@gmail','$2y$13$kBWenL8/T2NJPcdsq5YEAeVbLd4RwS32MqOCiVN9s441EsKFE/JCe','ahlam','ahlam','user',0),(36,'trzghjyuj','$2y$13$VpN3a76wCQc03Oud73pGae0Qb5kcsJ0/EwyHz/qlhft/dipOw7GJC','employe','employe','user',0),(37,'moon@gmail.com','$2y$13$SoMxiVtsR3cY3cCrVLnKf.qlNF6uy33a/RU./fwLu2MH6v/xSrBau','moon','moon','employe',0);
/*!40000 ALTER TABLE `utilisateur` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-01-25 16:46:28
