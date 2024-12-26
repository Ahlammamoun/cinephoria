import React, { useEffect, useState, useContext } from "react";
import axios from "axios";
import { UserContext } from "./UserContext";
import { QRCodeCanvas } from "qrcode.react"; // Utiliser QRCodeCanvas


const SeancesMobile = () => {
    const { user, loading } = useContext(UserContext);
    const [seances, setSeances] = useState([]);
    const [loadingSeances, setLoadingSeances] = useState(true);
    const [error, setError] = useState(null);
    const [selectedSeance, setSelectedSeance] = useState(null); // Pour stocker la séance sélectionnée
    const [numberOfSeats, setNumberOfSeats] = useState(1); // Nombre de sièges sélectionnés


    // Charger les séances
    useEffect(() => {
        const fetchSeances = async () => {
            try {
                const response = await axios.get("http://localhost:8000/api/seances");
                setSeances(response.data.seances || []);
            } catch (err) {
                setError(err.response?.data?.error || "Erreur lors du chargement des séances.");
            } finally {
                setLoadingSeances(false);
            }
        };

        if (!loading && user) {
            fetchSeances();
        } else if (!loading && !user) {
            setError("Vous devez être connecté pour consulter vos séances.");
            setLoadingSeances(false);
        }
    }, [user, loading]);

    // Gérer le clic sur une séance
    const handleSelectSeance = (seance) => {
        setSelectedSeance(seance);
        setNumberOfSeats(seance.sieges.length); // Initialiser au nombre de sièges disponibles
    };

    // Fermer le QR code
    const handleCloseQR = () => {
        setSelectedSeance(null);
    };

    if (loading || loadingSeances) {
        return <div className="loading">Chargement des séances...</div>;
    }

    if (error) {
        return <div className="error">{error}</div>;
    }

    return (
        <div className="mobile-container">
            <h1>Mes Séances</h1>
            {seances.length === 0 ? (
                <p>Aucune séance disponible.</p>
            ) : (
                <ul className="seance-list">
                    {seances.map((seance) => (
                        <li key={seance.id} className="seance-item" onClick={() => handleSelectSeance(seance)}>
                            <img
                                src={seance.film.affiche}
                                alt={seance.film.title}
                                className="film-affiche"
                            />
                            <div className="seance-details">
                                <h3>{seance.film.title}</h3>
                                <p><strong>Date :</strong> {seance.date}</p>
                                <p><strong>Heure :</strong> {seance.heureDebut} - {seance.heureFin}</p>
                                <p><strong>Salle :</strong> {seance.salle}</p>
                                <p><strong>Sièges :</strong> {seance.sieges.join(", ")}</p>
                            </div>
                        </li>
                    ))}
                </ul>
            )}

            {/* Affichage du QR Code lorsqu'une séance est sélectionnée */}
            {selectedSeance && (
                <div className="popup">
                    <div className="popup-content">
                        <button className="close-button" onClick={handleCloseQR}>
                            &times;
                        </button>
                        <h2>{selectedSeance.film.title}</h2>
                        <p><strong>Date :</strong> {selectedSeance.date}</p>
                        <p><strong>Heure :</strong> {selectedSeance.heureDebut} - {selectedSeance.heureFin}</p>
                        <p><strong>Salle :</strong> {selectedSeance.salle}</p>

                        {/* Sélectionner le nombre de personnes */}
                        <label>
                            Nombre de sièges :
                            <input
                                type="number"
                                value={numberOfSeats}
                                onChange={(e) => {
                                    const value = parseInt(e.target.value, 10);
                                    if (value > 0 && value <= selectedSeance.sieges.length) { // Vérifie la limite
                                        setNumberOfSeats(value);
                                    }
                                }}
                                min="1"
                                max={selectedSeance.sieges.length} // Limite selon la séance
                            />
                        </label>


                        {/* Générer le QR Code */}
                        <QRCodeCanvas
                            value={JSON.stringify({
                                seance: selectedSeance.film.title,
                                date: selectedSeance.date,
                                heureDebut: selectedSeance.heureDebut,
                                heureFin: selectedSeance.heureFin,
                                salle: selectedSeance.salle,
                                siegesReserves: numberOfSeats // Envoyer le nombre correct de sièges
                            })}
                            size={200}
                        />

                    </div>
                </div>
            )}
        </div>
    );
};

export default SeancesMobile;

