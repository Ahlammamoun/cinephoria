import React, { useState } from "react";

const AddFilm = () => {
  // État pour les informations du film
  const [formData, setFormData] = useState({
    title: "",
    description: "",
    releaseDate: "",
    minimumAge: "",
    note: "",
    affiche: "",
  });

  // État pour la séance associée
  const [seance, setSeance] = useState({
    dateDebut: "",
    dateFin: "",
    salleId: "",
    qualiteId: "",
  });

  const [message, setMessage] = useState(null); // Message de succès ou d'erreur

  // Gestion des changements pour les champs du film
  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  // Gestion des changements pour les champs de la séance
  const handleSeanceChange = (e) => {
    const { name, value } = e.target;
    setSeance((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  // Gestion de la soumission du formulaire
  const handleSubmit = async (e) => {
    e.preventDefault();
    setMessage(null);

    try {
      // Combiner les données du film et de la séance dans une seule requête
      const requestData = {
        ...formData,
        seances: [seance], // Ajouter la séance dans un tableau
      };

      const response = await fetch("http://localhost:8000/api/add-film", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(requestData),
      });

      const data = await response.json();

      if (!response.ok) {
        setMessage({ type: "error", text: data.error || "Une erreur est survenue" });
      } else {
        setMessage({ type: "success", text: "Film et séance ajoutés avec succès !" });

        // Réinitialiser les formulaires
        setFormData({
          title: "",
          description: "",
          releaseDate: "",
          minimumAge: "",
          note: "",
          affiche: "",
        });
        setSeance({
          dateDebut: "",
          dateFin: "",
          salleId: "",
          qualiteId: "",
        });
      }
    } catch (err) {
      setMessage({ type: "error", text: "Erreur de connexion au serveur" });
    }
  };

  return (
    <div className="add-film-container">
      <h1>Ajouter un Film et une Séance</h1>
      {message && <div className={`message ${message.type}`}>{message.text}</div>}

      {/* Formulaire principal */}
      <form onSubmit={handleSubmit} className="film-form">
        <label>
          Titre:
          <input type="text" name="title" value={formData.title} onChange={handleChange} required />
        </label>

        <label>
          Description:
          <textarea name="description" value={formData.description} onChange={handleChange} required />
        </label>

        <label>
          Date de sortie:
          <input type="date" name="releaseDate" value={formData.releaseDate} onChange={handleChange} required />
        </label>

        <label>
          Âge minimum:
          <input type="number" name="minimumAge" value={formData.minimumAge} onChange={handleChange} required />
        </label>

        <label>
          Note:
          <input type="number" name="note" step="0.1" value={formData.note} onChange={handleChange} required />
        </label>

        <label>
          URL de l'affiche:
          <input type="text" name="affiche" value={formData.affiche} onChange={handleChange} required />
        </label>

        <h2>Séance associée</h2>
        <label>
          Date Début:
          <input
            type="datetime-local"
            name="dateDebut"
            value={seance.dateDebut}
            onChange={handleSeanceChange}
            required
          />
        </label>

        <label>
          Date Fin:
          <input
            type="datetime-local"
            name="dateFin"
            value={seance.dateFin}
            onChange={handleSeanceChange}
            required
          />
        </label>

        

        <label>
          Salle ID:
          <input
            type="number"
            name="salleId"
            value={seance.salleId}
            onChange={handleSeanceChange}
            required
          />
        </label>

        <label>
          Qualité ID:
          <input
            type="number"
            name="qualiteId"
            value={seance.qualiteId}
            onChange={handleSeanceChange}
            required
          />
        </label>

        <button type="submit">Ajouter le film et la séance</button>
      </form>
    </div>
  );
};

export default AddFilm;

