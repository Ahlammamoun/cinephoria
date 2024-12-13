import React, { useEffect, useState, useContext } from "react";
import axios from "axios";
import { UserContext } from "./UserContext";

const Commandes = () => {
    const { user, loading } = useContext(UserContext);
    const [reservations, setReservations] = useState([]);
    const [loadingReservations, setLoadingReservations] = useState(true);
    const [error, setError] = useState(null);
  
    useEffect(() => {
      const fetchReservations = async () => {
        try {
          console.log("Fetching reservations...");
          const response = await axios.get("http://localhost:8000/api/commandes");
          console.log("Response data:", response.data);
          setReservations(response.data.reservations || []);
        } catch (err) {
          console.error("Error fetching reservations:", err);
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
            {reservations.map((reservation) => (
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
                  <strong>Sièges réservés :</strong> {reservation.sieges.join(", ")}
                </p>
                <p>
                  <strong>Prix total :</strong> {reservation.prixTotal} €
                </p>
              </li>
            ))}
          </ul>
        )}
      </div>
    );
  };
  
  export default Commandes;