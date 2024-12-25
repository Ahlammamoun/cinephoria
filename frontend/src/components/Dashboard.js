import React, { useState, useEffect, useRef } from "react";

const Dashboard = () => {
    const [data, setData] = useState([]); // Données du graphique
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    // Référence au canvas pour dessiner
    const canvasRef = useRef(null);

    useEffect(() => {
        // Fonction pour charger les données depuis l'API
        const fetchData = async () => {
            try {
                const response = await fetch("http://localhost:8000/api/admin/reservations-stats");
                const result = await response.json();
                console.log(result);
                if (response.ok) {
                    setData(result);
                } else {
                    setError(result.error || "Erreur lors du chargement des données.");
                }
                setLoading(false);
            } catch (err) {
                setError("Erreur serveur.");
                setLoading(false);
            }
        };

        fetchData();
    }, []);

    // Fonction pour dessiner le graphique
    const drawChart = () => {
        const canvas = canvasRef.current;
        if (!canvas) return;

        const ctx = canvas.getContext("2d");
        const width = canvas.width;
        const height = canvas.height;

        // Nettoyer le canvas
        ctx.clearRect(0, 0, width, height);

        // Données pour le graphique
        const labels = data.map((item) => item.film); // Labels (titres des films)
        const values = data.map((item) => item.reservations); // Réservations

        // Paramètres du graphique
        const barWidth = 40;
        const barSpacing = 30;
        const padding = 70; // Espace pour les axes et labels
        const chartHeight = height - 50 - padding;
        const maxReservations = Math.max(...values, 10); // Valeur max (minimum 10 pour éviter des barres écrasées)
        const scaleFactor = chartHeight / maxReservations;

        // Dessiner l'axe des X et Y
        ctx.strokeStyle = "white";
        ctx.lineWidth = 1;

        // Axe Y
        ctx.beginPath();
        ctx.moveTo(padding, 0);
        ctx.lineTo(padding, chartHeight);
        ctx.stroke();

        // Axe X
        ctx.beginPath();
        ctx.moveTo(padding, chartHeight);
        ctx.lineTo(width, chartHeight);
        ctx.stroke();

        // Dessiner les barres
        values.forEach((value, index) => {
            const barHeight = value * scaleFactor;
            const x = padding + index * (barWidth + barSpacing) + 30;;
            const y = chartHeight - barHeight;

            // Dessiner la barre
            ctx.fillStyle = "red";
            ctx.fillRect(x, y, barWidth, barHeight);

            // Afficher la valeur au-dessus de la barre
            ctx.fillStyle = "white";
            ctx.textAlign = "center";
            const textY = y - 10 < 20 ? y + 15 : y - 10; // Si trop haut, afficher à l'intérieur de la barre

            ctx.fillText(value, x + barWidth / 2, textY);
            // Ajouter le chiffre d'affaires en bas
            const chiffreAffaire = data[index].chiffreAffaire ?? 0; // Défaut à 0
            ctx.fillStyle = "red"; // Mettre la couleur en rouge
            ctx.fillText(`€${chiffreAffaire.toFixed(2)}`, x + barWidth / 2, height - 0);
            // Afficher les labels (rotation pour éviter le chevauchement)
            ctx.save();
            ctx.translate(x + barWidth / 2, chartHeight + 10);
            ctx.rotate(-Math.PI / 4); // Rotation de -45 degrés
            ctx.textAlign = "right";
            ctx.fillText(labels[index], 0, 0);
            ctx.restore();


            const totalChiffreAffaire = data.reduce(
                (sum, item) => sum + (item.chiffreAffaire ?? 0),
                0
            );
              
              // Affichage du total EN HAUT
              ctx.fillStyle = "red"; // Couleur blanche
              ctx.font = "15px Arial"; // Police
              ctx.textAlign = "center"; // Centré horizontalement
              ctx.fillText(
                `Total Chiffre d'Affaires : €${totalChiffreAffaire.toFixed(2)}`,
                width / 2, // Centrage horizontal
                20 // Position verticale en haut (ajuster si besoin)
            );
            

        });
    };


    // Appeler drawChart lorsque les données sont disponibles
    useEffect(() => {
        if (data.length > 0) {
            drawChart();
        }
    }, [data]);

    return (
        <div className="dashboard-container">
            <h1 className="graphique">Dashboard - Réservations par film (7 derniers jours)</h1>
            {loading && <p>Chargement des données...</p>}
            {error && <p className="error">{error}</p>}
            {!loading && !error && (
                <div>
                    <canvas ref={canvasRef} width={800} height={400}></canvas>
                </div>
            )}
        </div>
    );
};

export default Dashboard;
