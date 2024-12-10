import React, { useEffect, useState } from "react";


// const movies = [
//   { id: 1, title: "Movie 1", image: "path/to/image1.jpg" },
//   { id: 2, title: "Movie 2", image: "path/to/image2.jpg" },
//   { id: 3, title: "Movie 3", image: "path/to/image3.jpg" },
//   { id: 4, title: "Movie 4", image: "path/to/image4.jpg" },
//   { id: 5, title: "Movie 5", image: "path/to/image5.jpg" },
//   { id: 6, title: "Movie 6", image: "path/to/image6.jpg" },
//   { id: 7, title: "Movie 7", image: "path/to/image1.jpg" },
//   { id: 8, title: "Movie 8", image: "path/to/image2.jpg" },
//   { id: 9, title: "Movie 9", image: "path/to/image3.jpg" },
//   { id: 10, title: "Movie 10", image: "path/to/image4.jpg" },
//   { id: 11, title: "Movie 11", image: "path/to/image5.jpg" },
//   { id: 12, title: "Movie 12", image: "path/to/image6.jpg" },
// ];

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
<div className="home-container">
      <h1 className="release">Release <span>of the week</span></h1>
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
          </div>
        ))}
      </div>
    </div>
  );
};

export default Home;

