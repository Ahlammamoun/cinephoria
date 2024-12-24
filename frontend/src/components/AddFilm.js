import React, { useState } from "react";

const AddFilm = () => {
  const [formData, setFormData] = useState({
    title: "",
    description: "",
    releaseDate: "",
    minimumAge: "",
    note: "",
    affiche: "",
  });

  const [message, setMessage] = useState(null); // Message de succès ou d'erreur

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setMessage(null);

    try {
      const response = await fetch("http://localhost:8000/api/add-film", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(formData),
      });

      const data = await response.json();

      if (!response.ok) {
        setMessage({ type: "error", text: data.error || "Une erreur est survenue" });
      } else {
        setMessage({ type: "success", text: "Film ajouté avec succès !" });
        setFormData({
          title: "",
          description: "",
          releaseDate: "",
          minimumAge: "",
          note: "",
          affiche: "",
        });
      }
    } catch (err) {
      setMessage({ type: "error", text: "Erreur de connexion au serveur" });
    }
  };

  return (
    <div className="add-film-container">
      <h1>Ajouter un Film</h1>
      {message && (
        <div className={`message ${message.type}`}>
          {message.text}
        </div>
      )}
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

        <button type="submit">Ajouter le film</button>
      </form>
    </div>
  );
};

export default AddFilm;
