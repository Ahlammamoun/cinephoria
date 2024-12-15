import React, { useEffect, useState } from "react";
import { AiFillStar } from "react-icons/ai";


const Home = () => {

  const [movies, setMovies] = useState([]);
  const [selectedMovie, setSelectedMovie] = useState(null);

  const handleDescriptionToggle = (id) => {
    setSelectedMovie((prevSelected) => (prevSelected === id ? null : id));
  };



  useEffect(() => {
    // Récupérer les films depuis l'API Symfony
    fetch("http://localhost:8000/api/films")
      .then((response) => response.json())
      .then((data) => setMovies(data))
      .catch((error) => console.error("Erreur lors de la récupération des films :", error));
  }, []);

  if (!movies.length) {
    return <p>Chargement des films...</p>;
  }

  return (
  <div className="movie-carousel">
        {movies.map((movie) => (
          <div className="movie-item" key={movie.id}>
            <img
              src={movie.affiche}
              alt={movie.title}
              style={{ cursor: "pointer" }}
          
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
  );
};

export default Home;

