CREATE TABLE Film (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    releaseDate DATETIME,
    minimumAge INT,
    note FLOAT,
    isFavorite BOOLEAN
);

CREATE TABLE Genre (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE Film_Genre (
    film_id INT,
    genre_id INT,
    PRIMARY KEY (film_id, genre_id),
    FOREIGN KEY (film_id) REFERENCES Film(id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES Genre(id) ON DELETE CASCADE
);

CREATE TABLE Incident (
    id INT AUTO_INCREMENT PRIMARY KEY,
    description TEXT,
    dateSignalement DATETIME,
    resolu BOOLEAN,
    salle_id INT,
    FOREIGN KEY (salle_id) REFERENCES Salle(id) ON DELETE CASCADE
);

CREATE TABLE Qualite (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE Film_Qualite (
    film_id INT,
    qualite_id INT,
    PRIMARY KEY (film_id, qualite_id),
    FOREIGN KEY (film_id) REFERENCES Film(id) ON DELETE CASCADE,
    FOREIGN KEY (qualite_id) REFERENCES Qualite(id) ON DELETE CASCADE
);


CREATE TABLE Salle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero INT NOT NULL,
    capaciteTotale INT,
    capacitePMR INT,
    qualite_id INT,
    FOREIGN KEY (qualite_id) REFERENCES Qualite(id) ON DELETE SET NULL
);

CREATE TABLE Reservation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombreSieges INT,
    siegesReserves TEXT, 
    prixTotal FLOAT,
    utilisateur_id INT,
    seance_id INT,
    FOREIGN KEY (utilisateur_id) REFERENCES Utilisateur(id),
    FOREIGN KEY (seance_id) REFERENCES Seance(id)
);

CREATE TABLE Seance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dateDebut DATETIME,
    dateFin DATETIME,
    salle_id INT,
    film_id INT,
    FOREIGN KEY (salle_id) REFERENCES Salle(id) ON DELETE CASCADE,
    FOREIGN KEY (film_id) REFERENCES Film(id) ON DELETE CASCADE
);

CREATE TABLE Utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    nom VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL,
    requiresPasswordChange BOOLEAN DEFAULT FALSE
);

