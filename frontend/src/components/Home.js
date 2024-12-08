import React from "react";

const movies = [
  { id: 1, title: "Movie 1", image: "path/to/image1.jpg" },
  { id: 2, title: "Movie 2", image: "path/to/image2.jpg" },
  { id: 3, title: "Movie 3", image: "path/to/image3.jpg" },
  { id: 4, title: "Movie 4", image: "path/to/image4.jpg" },
  { id: 5, title: "Movie 5", image: "path/to/image5.jpg" },
  { id: 6, title: "Movie 6", image: "path/to/image6.jpg" },
  { id: 7, title: "Movie 7", image: "path/to/image1.jpg" },
  { id: 8, title: "Movie 8", image: "path/to/image2.jpg" },
  { id: 9, title: "Movie 9", image: "path/to/image3.jpg" },
  { id: 10, title: "Movie 10", image: "path/to/image4.jpg" },
  { id: 11, title: "Movie 11", image: "path/to/image5.jpg" },
  { id: 12, title: "Movie 12", image: "path/to/image6.jpg" },
];

const Home = () => {
  return (
    <div className="home-container">
      <h1>Les Films</h1>
      <div className="movie-carousel">
        {movies.map((movie) => (
          <div className="movie-item" key={movie.id}>
            <img src={movie.image} alt={movie.title} />
            <p>{movie.title}</p>
          </div>
        ))}
      </div>
    </div>
  );
};

export default Home;

