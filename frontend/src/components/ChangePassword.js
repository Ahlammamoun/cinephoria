import React, { useState } from "react";
import axios from "axios";

const ChangePassword = () => {
    const [oldPassword, setOldPassword] = useState("");
    const [newPassword, setNewPassword] = useState("");
    const [message, setMessage] = useState("");
    const [error, setError] = useState("");

    const handleChangePassword = async () => {
        try {
            const response = await axios.post(
                "http://localhost:8000/api/change-password",
                {
                    oldPassword,
                    newPassword,
                },
                {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("token")}`, // Remplacez par votre gestion de token
                    },
                }
            );
            setMessage(response.data.message);
            setError("");
        } catch (err) {
            setError(err.response?.data?.error || "Erreur lors de la modification.");
            setMessage("");
        }
    };

    return (
        <div className="change-password-container">
            <h2>Changer le mot de passe</h2>
            <input
                type="password"
                placeholder="Ancien mot de passe"
                value={oldPassword}
                onChange={(e) => setOldPassword(e.target.value)}
            />
            <input
                type="password"
                placeholder="Nouveau mot de passe"
                value={newPassword}
                onChange={(e) => setNewPassword(e.target.value)}
            />
            <button onClick={handleChangePassword}>Modifier</button>
            {message && <p className="success">{message}</p>}
            {error && <p className="error">{error}</p>}
        </div>
    );
};

export default ChangePassword;
