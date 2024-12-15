import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { AiFillStar } from "react-icons/ai";

const MoviesList = () => {
  const [movies, setMovies] = useState([]);
  const [cinemas, setCinemas] = useState([]); // Cinémas disponibles
  const [genres, setGenres] = useState([]); // Genres disponibles
  const [selectedMovie, setSelectedMovie] = useState(null);
  const [showPopup, setShowPopup] = useState(false); // Gérer la visibilité
  const [selectedMovieDetails, setSelectedMovieDetails] = useState(null);
  // États pour les filtres
  const [cinemaId, setCinemaId] = useState("");
  const [genre, setGenre] = useState("");
  const [date, setDate] = useState("");
  const navigate = useNavigate();

  const handleDescriptionToggle = (id) => {
    setSelectedMovie((prevSelected) => (prevSelected === id ? null : id));
  };

  const fetchMovies = () => {
    // Construire l'URL avec les paramètres de filtre
    const params = new URLSearchParams();
    if (cinemaId) params.append("cinemaId", cinemaId);
    if (genre) params.append("genre", genre);
    if (date) params.append("date", date);

    fetch(`http://localhost:8000/api/films?${params.toString()}`)
      .then((response) => response.json())
      .then((data) => setMovies(data.films)) // Charger uniquement les films
      .catch((error) => console.error("Erreur lors de la récupération des films :", error));
  };

  // const handleMovieClick = (movie) => {
  //   setSelectedMovieDetails(movie); // Mettre à jour les détails du film sélectionné
  // };

  const fetchCinemas = () => {
    fetch("http://localhost:8000/api/films") // Appel vers le nouvel endpoint
      .then((response) => response.json())
      .then((data) => {
        console.log("API Response:", data); // Inspectez la clé "cinemas" ici
        setCinemas(data.cinemas || []); // Fallback en cas de clé manquante
      })
      .catch((error) => console.error("Erreur lors de la récupération des cinémas :", error));
  };

  const fetchGenres = () => {
    fetch("http://localhost:8000/api/films")
      .then((response) => response.json())
      .then((data) => setGenres(data.genres))
      .catch((error) => console.error("Erreur lors de la récupération des genres :", error));
  };

  // Charger les cinémas et genres une seule fois au montage
  useEffect(() => {
    fetchCinemas(); // Charger les cinémas
    fetchGenres(); // Charger les genres
  }, []);

  // Charger les films à chaque changement de filtre
  useEffect(() => {
    fetchMovies();
  }, [cinemaId, genre, date]);


  const openPopup = (movie) => {
    setSelectedMovieDetails(movie); // Stocker les détails du film sélectionné
    setShowPopup(true); // Afficher la popup
  };

  const closePopup = () => {
    setSelectedMovieDetails(null); // Réinitialiser les détails du film
    setShowPopup(false); // Fermer la popup
  };

  const qualityPrices = {
    "Standard": 10,
    "3D": 12,
    "4K": 15,
    "4DX": 22,
    "IMAX": 30,
  };

  const handleReservation = (seance, movie, cinema) => {
    navigate("/reservation", {
      state: {
        seanceId: seance.id,
        seanceDetails: seance,
        movieTitle: movie.title,
        cinemaDetails: cinema,
      },
    });
  };

  if (!movies.length) {
    return <p className="chargement ">Chargement des films...</p>;
  }

  return (
    <div className="home-container">
      <h1 className="release">
        <span>the movies</span>
      </h1>

      {/* Filtres par cinéma */}
      <div className="select-wrapper">
        <select
          className="select-custom"
          value={cinemaId}
          onChange={(e) => setCinemaId(e.target.value)}
        >
          <option value="">Select Cinema</option>
          {cinemas.map((cinema) => (
            <option key={cinema.id} value={cinema.id}>
              {cinema.name} {cinema.adresse}
            </option>
          ))}
        </select>
      </div>

      {/* Filtres par genre */}
      <div className="select-wrapper">
        <select
          className="select-custom"
          value={genre}
          onChange={(e) => setGenre(e.target.value)}
        >
          <option value="">Select Genre</option>
          {genres.map((g) => (
            <option key={g.id} value={g.name}>
              {g.name}
            </option>
          ))}
        </select>
      </div>

      {/* Filtre par date */}
      <div className="select-wrapper">
        <input
          type="date"
          className="select-custom"
          value={date}
          onChange={(e) => setDate(e.target.value)}
        />
      </div>

      {/* Liste des films */}
      <div className="movie-carousel">
        {movies.map((movie) => (
          <div className="movie-item" key={movie.id}>
            <img
              src={movie.affiche}
              alt={movie.title}
              style={{ cursor: "pointer" }}
              onClick={() => openPopup(movie)} // Ouvrir la popup au clic sur le film
            />
            <p>{movie.title}</p>
            <p className="movie-description">
              {selectedMovie === movie.id
                ? movie.description
                : `${movie.description.slice(0, 50)}...`}
              <span
                onClick={() => handleDescriptionToggle(movie.id)}
                style={{
                  color: "#e50914",
                  cursor: "pointer",
                  marginLeft: "5px",
                }}
              >
                {selectedMovie === movie.id ? "-" : "+"}
              </span>
            </p>
            <div className="movie-info">
              <div className="info-item">
                <span>{movie.minimumAge}+</span>
              </div>
              <div className="info-item">
                <AiFillStar
                  size={10}
                  style={{ color: "gold", marginRight: "5px" }}
                />
                <span>{movie.note}/10</span>
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Popup - Déplacée en dehors de la boucle */}
      {showPopup && selectedMovieDetails && (
        <div className="popup">
          <div className="popup-content">
            <button className="close-button" onClick={closePopup}>
              ×
            </button>
            <h2>{selectedMovieDetails.title}</h2>
            <p>{selectedMovieDetails.description}</p>
            <h3>Séances</h3>
            <table className="seance-table">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Start Time</th>
                  <th>End Time</th>
                  <th>Quality</th>
                  <th>Price</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                {selectedMovieDetails.seances.map((seance) => (
                  <tr key={seance.id}>
                    <td>{new Date(seance.dateDebut).toLocaleDateString()}</td>
                    <td>{new Date(seance.dateDebut).toLocaleTimeString()}</td>
                    <td>{new Date(seance.dateFin).toLocaleTimeString()}</td>
                    <td>{seance.qualite}</td>
                    <td>${qualityPrices[seance.qualite?.trim() || "Standard"]}</td>
                    <td>
                      <button
                        className="reserve-button"
                        onClick={() => handleReservation(seance, selectedMovieDetails, cinemas.find((cinema) => cinema.id === cinemaId))}
                      >
                        Reserve
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}
    </div>

  );
};

export default MoviesList;

