import React, { useEffect, useState } from 'react';
import { AiFillStar } from "react-icons/ai";

const HomePage = () => {
  const [movies, setMovies] = useState([]);

  useEffect(() => {
    fetch('http://localhost:8000/api/')
      .then((response) => response.json())
      .then((data) => setMovies(data))
      .catch((error) => console.error('Erreur de chargement des films:', error));
  }, []);

  return (
    <div>
        <img className = "logo" src={`/assets/images/image0.jpeg`} />
      <div className="movies-list">
        {movies.map((movie) => (
          <div key={movie.id} className="movie-card">
            <img src={`/assets/images/${movie.affiche}`} alt={movie.title} />
            <h2>{movie.title}</h2>
            <p>{movie.description}</p>
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
    </div>
  );
};


export default HomePage;


