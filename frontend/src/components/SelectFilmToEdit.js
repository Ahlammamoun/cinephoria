import React, { useState, useEffect } from "react";
import EditFilm from "./EditFilm"; // Réutilisation du composant EditFilm

const SelectFilmToEdit = () => {
  const [films, setFilms] = useState([]); // Liste des films
  const [selectedFilmId, setSelectedFilmId] = useState(null); // Film sélectionné
  const [message, setMessage] = useState(null); // Message d'état (succès/erreur)

  // Charger la liste des films
  useEffect(() => {
    const fetchFilms = async () => {
      try {
        const response = await fetch("http://localhost:8000/api/admin/list-films");
        const data = await response.json();

        if (response.ok) {
          setFilms(data); // Remplir la liste des films
        } else {
          setMessage({ type: "error", text: "Impossible de charger les films." });
        }
      } catch (err) {
        setMessage({ type: "error", text: "Erreur serveur." });
      }
    };

    fetchFilms(); // Chargement initial des films
  }, []);

  // Gestion du changement de sélection
  const handleSelectChange = (e) => {
    setSelectedFilmId(e.target.value); // Met à jour l'ID sélectionné
  };

  // Supprimer un film
  const handleDelete = async (id) => {
    if (window.confirm("Voulez-vous vraiment supprimer ce film et ses séances associées ?")) {
      try {
        const response = await fetch(`http://localhost:8000/api/admin/delete-film/${id}`, {
          method: "DELETE",
        });

        const data = await response.json();

        if (response.ok) {
          setMessage({ type: "success", text: data.success });
          setFilms(films.filter((film) => film.id !== id)); // Mettre à jour la liste après suppression
          setSelectedFilmId(null); // Réinitialiser la sélection
        } else {
          setMessage({ type: "error", text: data.error || "Une erreur est survenue." });
        }
      } catch (err) {
        setMessage({ type: "error", text: "Erreur serveur." });
      }
    }
  };

  return (
    <div className="edit">
      <h1>Modifier ou Supprimer un Film</h1>
      {message && <div className={`message ${message.type}`}>{message.text}</div>}

      {/* Liste déroulante pour sélectionner un film */}
      <label className="edit">Choisir un film :</label>
      <select onChange={handleSelectChange} value={selectedFilmId || ""}>
        <option value="">-- Sélectionner un film --</option>
        {films.map((film) => (
          <option key={film.id} value={film.id}>
            {film.title}
          </option>
        ))}
      </select>

      {/* Affichage du formulaire d'édition uniquement si un film est sélectionné */}
      {selectedFilmId && (
        <>
          <EditFilm filmId={selectedFilmId} />

          {/* Bouton pour supprimer le film */}
          <button
            style={{ marginTop: "10px", backgroundColor: "red", color: "white" }}
            onClick={() => handleDelete(selectedFilmId)}
          >
            Supprimer le film
          </button>
        </>
      )}
    </div>
  );
};

export default SelectFilmToEdit;

