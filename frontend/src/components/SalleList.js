import React, { useState, useEffect } from "react";

const SalleManager = () => {
    const [salles, setSalles] = useState([]); // Liste des salles
    const [formData, setFormData] = useState({
        id: null,
        numero: "",
        capaciteTotale: "",
        capacitePMR: "",
        qualite: "",
    }); // Formulaire pour ajouter ou modifier
    const [message, setMessage] = useState(null); // Message d'état
    const [editing, setEditing] = useState(false); // Mode édition
    const [qualites, setQualites] = useState([]);

    // Charger les salles depuis l'API
    const fetchSalles = async () => {
        try {
            const response = await fetch("http://localhost:8000/api/admin/list-salles");
            const data = await response.json();
            setSalles(data);
        } catch (err) {
            setMessage({ type: "error", text: "Erreur de chargement des salles." });
        }
    };

    useEffect(() => {
        fetchSalles();
    }, []);


    useEffect(() => {
        const fetchQualites = async () => {
            try {
                const response = await fetch("http://localhost:8000/api/admin/list-qualites");
                const data = await response.json();
                setQualites(data); // Stocker la liste des qualités
                // console.log(data);
            } catch (err) {
                setMessage({ type: "error", text: "Erreur de chargement des qualités." });
            }
        };
        fetchQualites();
    }, []);


    // Gérer les changements dans le formulaire
    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData((prev) => ({
            ...prev,
            [name]: value,
        }));
    };

    // Ajouter ou modifier une salle
    const handleSubmit = async (e) => {
        e.preventDefault();
        setMessage(null);

        const url = editing
            ? `http://localhost:8000/api/admin/edit-salle/${formData.id}`
            : "http://localhost:8000/api/admin/add-salle";

        const method = editing ? "PUT" : "POST";

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(formData),
            });

            const data = await response.json();

            if (response.ok) {
                setMessage({ type: "success", text: data.success });
                fetchSalles(); // Rafraîchir la liste
                setFormData({
                    id: null,
                    numero: "",
                    capaciteTotale: "",
                    capacitePMR: "",
                    qualite: "",
                });
                setEditing(false); // Quitter le mode édition
            } else {
                setMessage({ type: "error", text: data.error });
            }
        } catch (err) {
            setMessage({ type: "error", text: "Erreur serveur." });
        }
    };

    // Supprimer une salle
    const handleDelete = async (id) => {
        if (window.confirm("Voulez-vous vraiment supprimer cette salle ?")) {
            try {
                const response = await fetch(
                    `http://localhost:8000/api/admin/delete-salle/${id}`,
                    { method: "DELETE" }
                );

                const data = await response.json();
                if (response.ok) {
                    setMessage({ type: "success", text: data.success });
                    fetchSalles(); // Rafraîchir la liste
                } else {
                    setMessage({ type: "error", text: data.error });
                }
            } catch (err) {
                setMessage({ type: "error", text: "Erreur serveur." });
            }
        }
    };

    // Remplir le formulaire pour l'édition
    const handleEdit = (salle) => {
        setFormData(salle);
        setEditing(true); // Passer en mode édition
    };

    return (
        <div className="salle-container">
            <h1>Gestion des Salles</h1>

            {message && (
                <div className={`message ${message.type}`}>{message.text}</div>
            )}

            {/* Formulaire pour ajouter/modifier une salle */}
            <form onSubmit={handleSubmit} className="salle-form">
                <label>
                    Numéro :
                    <input
                        type="number"
                        name="numero"
                        value={formData.numero}
                        onChange={handleChange}
                        required
                    />
                </label>

                <label>
                    Capacité Totale :
                    <input
                        type="number"
                        name="capaciteTotale"
                        value={formData.capaciteTotale}
                        onChange={handleChange}
                        required
                    />
                </label>

                <label>
                    Capacité PMR :
                    <input
                        type="number"
                        name="capacitePMR"
                        value={formData.capacitePMR}
                        onChange={handleChange}
                        required
                    />
                </label>

                <label>
                    Qualité :
                    <select
                        name="qualite"
                        value={formData.qualite}
                        onChange={handleChange}
                        required
                    >
                        <option value="">-- Sélectionnez une qualité --</option>
                        {qualites.map((qualite) => (
                            <option key={qualite.id} value={qualite.id}>
                                {qualite.nom}
                            </option>
                        ))}
                    </select>
                </label>


                <button type="submit">
                    {editing ? "Modifier la Salle" : "Ajouter une Salle"}
                </button>
            </form>

            {/* Liste des salles */}
            <ul className="salle-list">
                {salles.map((salle) => (
                    <li key={salle.id} className="salle-item">
                        <strong>Numéro:</strong> {salle.id} |{" "}
                        <strong>Capacité Totale:</strong> {salle.capaciteTotale} |{" "}
                        <strong>PMR:</strong> {salle.capacitePMR} |{" "}
                        <strong>Qualité:</strong> {salle.qualite ? salle.qualite.nom : "Non définie"} {/* Corrigé ici */}
                        <button onClick={() => handleEdit(salle)}>Modifier</button>
                        <button
                            style={{ backgroundColor: "red", color: "white" }}
                            onClick={() => handleDelete(salle.id)}
                        >
                            Supprimer
                        </button>
                    </li>
                ))}
            </ul>

        </div>
    );
};

export default SalleManager;
