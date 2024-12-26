import React, { useState } from "react";
import axios from "axios";

const ForgotPassword = () => {
    const [login, setLogin] = useState("");
    const [message, setMessage] = useState("");
    const [error, setError] = useState("");

    const handleForgotPassword = async () => {
        try {
            const response = await axios.post("http://localhost:8000/api/forgot-password", {
                login,
            });
            setMessage(response.data.message);
            setError("");
        } catch (err) {
            setError(err.response?.data?.error || "Erreur lors de la demande.");
            setMessage("");
        }
    };

    return (
        <div className="forgot-password-container">
            <h2>Mot de passe oublié</h2>
            <input
                type="text"
                placeholder="Entrez votre identifiant ou email"
                value={login}
                onChange={(e) => setLogin(e.target.value)}
            />
            <button onClick={handleForgotPassword}>Réinitialiser le mot de passe</button>
            {message && <p className="success">{message}</p>}
            {error && <p className="error">{error}</p>}
        </div>
    );
};

export default ForgotPassword;
