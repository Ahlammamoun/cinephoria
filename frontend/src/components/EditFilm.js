import React, { useState, useEffect } from "react";

const EditFilm = ({ filmId }) => {
    const [formData, setFormData] = useState({
        title: "",
        description: "",
        releaseDate: "",
        minimumAge: "",
        note: "",
        affiche: "",
        seances: [], // Tableau pour les séances associées
    });

    const [message, setMessage] = useState(null);

    // Charger les données du film et des séances associées
    useEffect(() => {
        const fetchFilm = async () => {
            try {
                const response = await fetch(
                    `http://localhost:8000/api/admin/get-film-with-seances/${filmId}`,
                    {
                        method: "GET",
                    }
                );

                const data = await response.json();

                if (response.ok) {
                    setFormData(data); // Charger les données du film et des séances associées
                } else {
                    setMessage({ type: "error", text: data.error || "Film introuvable." });
                }
            } catch (err) {
                setMessage({ type: "error", text: "Erreur serveur." });
            }
        };

        if (filmId) {
            fetchFilm(); // Charger uniquement si un film est sélectionné
        }
    }, [filmId]);

    // Gestion des champs du film
    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData((prev) => ({
            ...prev,
            [name]: value,
        }));
    };

    // Gestion des séances associées
    const handleSeanceChange = (index, key, value) => {
        const updatedSeances = [...formData.seances];
        updatedSeances[index][key] = value; // Mise à jour d'une séance spécifique
        setFormData({ ...formData, seances: updatedSeances });
    };

    // Soumission du formulaire
    const handleSubmit = async (e) => {
        e.preventDefault();
        setMessage(null);

        try {
            const response = await fetch(
                `http://localhost:8000/api/admin/edit-film-with-seances/${filmId}`,
                {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(formData),
                }
            );

            const data = await response.json();

            if (!response.ok) {
                setMessage({
                    type: "error",
                    text: data.error || "Une erreur est survenue",
                });
            } else {
                setMessage({
                    type: "success",
                    text: "Film et séances modifiés avec succès !",
                });
            }
        } catch (err) {
            setMessage({ type: "error", text: "Erreur de connexion au serveur" });
        }
    };

    return (
        <div className="edit-film-container">
            {message && (
                <div className={`message ${message.type}`}>
                    {message.text}
                </div>
            )}
            <form onSubmit={handleSubmit} className="film-form">
                {/* Champs pour les informations du film */}
                <label>
                    Titre:
                    <input
                        type="text"
                        name="title"
                        value={formData.title}
                        onChange={handleChange}
                        required
                    />
                </label>

                <label>
                    Description:
                    <textarea
                        name="description"
                        value={formData.description}
                        onChange={handleChange}
                        required
                    />
                </label>

                <label>
                    Date de sortie:
                    <input
                        type="date"
                        name="releaseDate"
                        value={formData.releaseDate}
                        onChange={handleChange}
                        required
                    />
                </label>

                <label>
                    Âge minimum:
                    <input
                        type="number"
                        name="minimumAge"
                        value={formData.minimumAge}
                        onChange={handleChange}
                        required
                    />
                </label>

                <label>
                    Note:
                    <input
                        type="number"
                        name="note"
                        step="0.1"
                        value={formData.note}
                        onChange={handleChange}
                        required
                    />
                </label>

                <label>
                    URL de l'affiche:
                    <input
                        type="text"
                        name="affiche"
                        value={formData.affiche}
                        onChange={handleChange}
                        required
                    />
                </label>
                {formData.seances.map((seance, index) => (
                    <div key={seance.id} className="seance-item">
                        <label>
                            Date de début:
                            <input
                                type="datetime-local"
                                value={seance.dateDebut || ""}
                                onChange={(e) =>
                                    handleSeanceChange(index, "dateDebut", e.target.value)
                                }
                                required
                            />
                        </label>
                        <label>
                            Date de fin:
                            <input
                                type="datetime-local"
                                value={seance.dateFin || ""}
                                onChange={(e) =>
                                    handleSeanceChange(index, "dateFin", e.target.value)
                                }
                                required
                            />
                        </label>
                       
                        <label>
                            Salle:
                            <input
                                type="number"
                                value={seance.salle || ""}
                                onChange={(e) =>
                                    handleSeanceChange(index, "salleId", e.target.value)
                                }
                                required
                            />
                        </label>
                        <label>
                            Qualité:
                            <input
                                type="number"
                                value={seance.qualite || ""}
                                onChange={(e) =>
                                    handleSeanceChange(index, "qualiteId", e.target.value)
                                }
                                required
                            />
                        </label>
                    </div>
                ))}

                <button type="submit">Modifier le film et les séances</button>
            </form>
        </div>
    );
};

export default EditFilm;


