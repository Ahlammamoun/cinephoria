import React, { useState, useEffect } from "react";
import axios from "axios";

const ReservationPage = () => {
    const [cinemas, setCinemas] = useState([]);
    const [selectedCinema, setSelectedCinema] = useState("");
    const [films, setFilms] = useState([]);
    const [selectedFilm, setSelectedFilm] = useState("");
    const [seances, setSeances] = useState([]);
    const [selectedSeance, setSelectedSeance] = useState("");
    const [numSeats, setNumSeats] = useState(1);
    const [price, setPrice] = useState(0);
    const [response, setResponse] = useState(null);
    const [error, setError] = useState(null);
    const [email, setEmail] = useState(""); // État pour stocker l'e-mail

    const seance = seances.find((s) => s.id === parseInt(selectedSeance, 10));
    const maxSeats = seance ? seance.availableSeats : 10;
    const qualityPrices = {
        Standard: 10,
        "3D": 12,
        "4K": 15,
        "4DX": 22,
        "IMAX": 30,
    };
    const [availableSeats, setAvailableSeats] = useState([]); // Liste des sièges disponibles
    const [selectedSeats, setSelectedSeats] = useState([]);
    console.log("Nombre maximum de sièges :", maxSeats);

    // Récupération des cinémas depuis l'API
    useEffect(() => {
        const fetchCinemas = async () => {
            try {
                const response = await axios.get("http://localhost:8000/api/reservation");
                // console.log("Cinemas :", response.data);
                // response.data.forEach(cinema => {
                //     console.log("Cinema", cinema);
                //     console.log("Properties:", Object.keys(cinema)); // Affiche toutes les propriétés de chaque objet
                // });

                setCinemas(response.data);

            } catch (err) {
                console.error("Erreur lors de la récupération des cinémas :", err);
            }
        };
        fetchCinemas();
    }, []);

    // Récupération des films pour le cinéma sélectionné
    useEffect(() => {
        if (selectedCinema) {
            const fetchFilms = async () => {
                try {
                    const response = await axios.get(
                        `http://localhost:8000/api/reservation/films/${selectedCinema}`
                    );
                    // console.log("films :", response.data);
                    setFilms(response.data);
                } catch (err) {
                    console.error("Erreur lors de la récupération des films :", err);
                }
            };
            fetchFilms();
        }
    }, [selectedCinema]);

    // Récupération des séances pour le film sélectionné
    useEffect(() => {
        if (selectedFilm) {
            const fetchSeances = async () => {
                try {
                    const response = await axios.get(
                        `http://localhost:8000/api/reservation/seances/${selectedCinema}/${selectedFilm}`
                    );
                    console.log("Séances récupérées :", response.data.data);
                    setSeances(response.data.data || []);

                } catch (err) {
                    console.error("Erreur lors de la récupération des séances :", err);
                }
            };
            fetchSeances();
        }
    }, [selectedFilm, selectedCinema]);

    // Calcul du prix en fonction de la qualité et du nombre de sièges
    useEffect(() => {
        if (selectedSeance) {
            const seance = seances.find((s) => s.id === parseInt(selectedSeance));
            console.log("Séance sélectionnée :", seance);
            if (seance) {
                const qualityPrice = qualityPrices[seance.qualite] || 10; // Par défaut, 10 € si la qualité n'est pas reconnue
                console.log("Prix unitaire par qualité :", qualityPrice);
                setPrice(qualityPrice * numSeats);
                const totalSeats = seance.availableSeats; // Nombre total de sièges
                const reservedSeats = seance.reservedSeats || []; // Sièges déjà réservés (exemple : ["A1", "B2"])
                const allSeats = Array.from({ length: totalSeats }, (_, i) => `S${i + 1}`);
                const freeSeats = allSeats.filter((seat) => !reservedSeats.includes(seat)); // Filtrer les sièges réservés
                console.log("Nombre total de sièges :", totalSeats);
                console.log("Sièges réservés :", reservedSeats);
                console.log("Sièges disponibles :", freeSeats);
                setAvailableSeats(freeSeats); // Mettre à jour la liste des sièges disponibles
            }
        }
    }, [selectedSeance, numSeats, seances]);



    // Gestion de la réservation
    const handleReservation = async () => {
        if (!selectedSeance) {
            setError("Veuillez sélectionner une séance.");
            return;
        }
        if (!email) {
            setError("Veuillez entrer une adresse e-mail valide.");
            return;
        }
        const payload = {
            seanceId: selectedSeance,
            userId: 1, // ID utilisateur simulé pour les tests
            seats: selectedSeats,
            price: price,
            email: email,
        };

        try {
            const response = await axios.post(
                "http://localhost:8000/api/reservation/create",
                payload
            );
            setResponse(response.data);
            setError(null);
        } catch (err) {
            setError(err.response?.data?.error || "Une erreur est survenue.");
        }
    };

    useEffect(() => {
        console.log("Mise à jour du nombre de sièges (numSeats) :", numSeats);
        if (selectedSeats.length > numSeats) {
            setSelectedSeats([]);
        }
    }, [numSeats]);


    const handleSeatSelection = (seat) => {
        console.log("Seat clicked:", seat);
        console.log("Currently selected seats:", selectedSeats);
        console.log("Maximum seats allowed:", numSeats);

        if (selectedSeats.includes(seat)) {
            console.log("Deselecting seat:", seat);
            setSelectedSeats((prevSeats) => prevSeats.filter((s) => s !== seat)); // Déselectionner
        } else if (selectedSeats.length < numSeats) {
            console.log("Selecting seat:", seat);
            setSelectedSeats((prevSeats) => [...prevSeats, seat]); // Ajouter un siège
        } else {
            console.warn(`Limite atteinte (${numSeats} sièges). Impossible de sélectionner davantage.`);
            alert(`Vous avez atteint la limite de ${numSeats} siège(s) sélectionné(s) !`);
        }
    };



    return (
        <div className="reservation-container">
            <h1>Réserver une séance</h1>

            {/* Sélection du cinéma */}
            <div className="select-container">
                <label htmlFor="cinema-select">Cinéma :</label>
                <select
                    id="cinema-select"
                    className="styled-select"
                    onChange={(e) => setSelectedCinema(e.target.value)}
                    value={selectedCinema}
                >
                    <option value="">Sélectionnez un cinéma</option>
                    {cinemas.map((cinema) => (
                        <option key={cinema.id} value={cinema.id}>
                            {cinema.nom} {cinema.adresse}
                        </option>
                    ))}
                </select>
            </div>

            {/* Sélection du film */}
            {films.length > 0 && (
                <div className="select-container">
                    <label htmlFor="film-select">Film :</label>
                    <select
                        id="film-select"
                        className="styled-select"
                        onChange={(e) => setSelectedFilm(e.target.value)}
                        value={selectedFilm}
                    >
                        <option value="">Sélectionnez un film</option>
                        {films.map((film) => (
                            <option key={film.id} value={film.id}>
                                {film.title}
                            </option>
                        ))}
                    </select>
                </div>
            )}

            {/* Sélection de la séance */}
            {seances.length > 0 && (
                <div className="seance-container">
                    <h3 className="seance">Choisissez une séance :</h3>
                    <ul className="seance-list">
                        {seances.map((seance) => (
                            <li
                                key={seance.id}
                                className={`seance-item ${selectedSeance === seance.id ? 'selected' : ''}`}
                                onClick={() => {
                                    console.log("Séance cliquée :", seance.id);
                                    setSelectedSeance(seance.id, 10);
                                }}
                            >
                                <div className="seance-time">
                                    <strong>Heure :</strong> {seance.dateDebut} - {seance.dateFin}
                                </div>
                                <div className="seance-quality">
                                    <strong>Qualité :</strong> {seance.qualite}
                                </div>
                                <div className="seance-room">
                                    <strong>Salle :</strong> {seance.salle}
                                </div>
                            </li>
                        ))}
                    </ul>
                </div>
            )}
            {/* Nombre de sièges */}
            {selectedSeance && (
                <div className="seats-selection">
                    <h3 className="siege">Sélectionnez vos sièges :</h3>
                    {/* Ajout de contrôles pour gérer le nombre de sièges dynamiquement */}
                    <div className="num-seats-control">
                        <button
                            onClick={() => setNumSeats((prev) => Math.max(1, prev - 1))}
                            disabled={numSeats <= 1}
                        >
                            -
                        </button>
                        <span>{numSeats} sièges</span>
                        <button
                            onClick={() => setNumSeats((prev) => Math.min(maxSeats, prev + 1))}
                            disabled={numSeats >= maxSeats}
                        >
                            +
                        </button>
                    </div>

                    {/* Affichage des sièges disponibles */}
                    <div className="seats-container">
                        {availableSeats.map((seat) => (
                            <button
                                key={seat}
                                className={`seat ${selectedSeats.includes(seat) ? 'selected' : ''}`}
                                onClick={() => handleSeatSelection(seat)}
                                disabled={
                                    selectedSeats.length >= numSeats && !selectedSeats.includes(seat) // Désactiver si limite atteinte
                                }
                            >
                                {seat}
                            </button>
                        ))}
                    </div>
                    <p>Sièges sélectionnés : {selectedSeats.join(", ")}</p>
                </div>
            )}

            <div className="email-input">
                <label className="email-input">Adresse e-mail :</label>
                <input
                    id="email-input"
                    type="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder="Entrez votre e-mail"
                    required
                />
            </div>
            {/* Prix et validation */}
            {price > 0 && (
                <div>
                    <p>Prix total : {price} €</p>
                    <button onClick={handleReservation}>Réserver</button>
                </div>
            )}

            {/* Affichage de la réponse ou des erreurs */}
            {response && (
                <div className="response-container">
                    <h2>Réservation réussie !</h2>
                    <p><strong>ID de la réservation :</strong> {response.reservationId}</p>
                    <p> {numSeats} <strong>Siège reservé</strong></p>
                    <p><strong>Prix total validé :</strong> {response.priceValidated} €</p>
                    {response.remainingSeats && (
                        <div>
                            <strong>Sièges restants :</strong>
                            <ul>
                                {response.remainingSeats.map((seat, index) => (
                                    <li key={index}>{seat}</li>
                                ))}
                            </ul>
                        </div>
                    )}
                </div>
            )}
            {error && <p className="error">{error}</p>}
        </div>
    );
};

export default ReservationPage;


