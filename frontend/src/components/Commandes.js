import React, { useEffect, useState, useContext } from "react";
import axios from "axios";
import { UserContext } from "./UserContext";

const Commandes = () => {
    const { user, loading } = useContext(UserContext);
    const [reservations, setReservations] = useState([]);
    const [loadingReservations, setLoadingReservations] = useState(true);
    const [error, setError] = useState(null);
    const [note, setNote] = useState({}); // Stocke les notes par réservation
    const [messages, setMessages] = useState({}); // Stocke les messages de confirmation/erreur

    // Récupérer les réservations de l'utilisateur
    useEffect(() => {
        const fetchReservations = async () => {
            try {
                const response = await axios.get("http://localhost:8000/api/commandes");
                setReservations(response.data.reservations || []);
            } catch (err) {
                setError(err.response?.data?.error || "Failed to load reservations.");
            } finally {
                setLoadingReservations(false);
            }
        };

        if (!loading && user) {
            fetchReservations();
        } else if (!loading && !user) {
            setError("Vous devez être connecté pour consulter vos réservations.");
            setLoadingReservations(false);
        }
    }, [user, loading]);

    // Gérer l'envoi de la note
    const handleSubmitNote = async (reservationId) => {
        try {
            const response = await axios.post(
                `http://localhost:8000/api/commandes/${reservationId}/note`,
                { note: note[reservationId] }
            );
            setMessages((prev) => ({ ...prev, [reservationId]: response.data.message }));
        } catch (err) {
            setMessages((prev) => ({
                ...prev,
                [reservationId]: err.response?.data?.error || "Erreur lors de la soumission.",
            }));
        }
    };

    if (loading || loadingReservations) {
        return <div className="loading">Chargement des données...</div>;
    }

    if (error) {
        return <div className="error">{error}</div>;
    }

    return (
        <div className="container">
            <h1>Mes Réservations</h1>
            {reservations.length === 0 ? (
                <p className="no-reservations">Aucune réservation disponible.</p>
            ) : (
                <ul>
                    {reservations.map((reservation) => {
                        const seanceFinie = new Date(reservation.seance.dateFin) < new Date(); // Vérifie si la séance est terminée

                        return (
                            <li key={reservation.id} className="reservation-item">
                                <h3>Film : {reservation.seance.film}</h3>
                                <p>
                                    <strong>Salle :</strong> {reservation.seance.salle}
                                </p>
                                <p>
                                    <strong>Horaires :</strong> {reservation.seance.dateDebut} -{" "}
                                    {reservation.seance.dateFin}
                                </p>
                                <p>
                                    <strong>Sièges réservés :</strong>{" "}
                                    {reservation.sieges.join(", ")}
                                </p>
                                <p>
                                    <strong>Prix total :</strong> {reservation.prixTotal} €
                                </p>

                                {seanceFinie ? (
                                    <div>
                                        <label>
                                            Note (1-5) :
                                            <input
                                                type="number"
                                                min="1"
                                                max="5"
                                                value={note[reservation.id] || ""}
                                                onChange={(e) =>
                                                    setNote((prev) => ({
                                                        ...prev,
                                                        [reservation.id]: e.target.value,
                                                    }))
                                                }
                                            />
                                        </label>
                                        <button onClick={() => handleSubmitNote(reservation.id)}>
                                            Noter
                                        </button>
                                        {messages[reservation.id] && (
                                            <p className="success">{messages[reservation.id]}</p>
                                        )}
                                    </div>
                                ) : (
                                    <p className="info">
                                        Vous pourrez noter ce film après la séance.
                                    </p>
                                )}
                            </li>
                        );
                    })}
                </ul>
            )}
        </div>
    );
};

export default Commandes;
