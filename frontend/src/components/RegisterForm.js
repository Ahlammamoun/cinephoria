import React, { useState } from "react";
import axios from "axios";

const RegisterForm = () => {
    // Définir les états pour les champs du formulaire
    const [login, setLogin] = useState("");
    const [password, setPassword] = useState("");
    const [prenom, setPrenom] = useState("");
    const [nom, setNom] = useState("");
    const [role, setRole] = useState("");
    const [error, setError] = useState(null);
    const [success, setSuccess] = useState(false);
  
    const handleSubmit = async (e) => {
      e.preventDefault();
  
      // Assurez-vous que tous les champs sont remplis
      if (!login || !password || !prenom || !nom || !role) {
        setError("All fields are required!");
        return;
      }
  
      try {
        // Envoi de la requête POST à l'API Symfony
        const response = await axios.post(
          "http://localhost:8000/api/register",
          {
            login,
            password,
            prenom,
            nom,
            role,
          }
        );
        
        // Si la création est réussie
        setSuccess(true);
        setError(null);
      } catch (err) {
        // Gestion des erreurs
        if (err.response) {
          setError(err.response.data.error || "Something went wrong");
        } else {
          setError("Network error");
        }
        setSuccess(false);
      }
    };
  
    return (
      <div>
        <h2>Register</h2>
        {success && <p>User created successfully!</p>}
        {error && <p style={{ color: "red" }}>{error}</p>}
        <form onSubmit={handleSubmit}>
          <div>
            <label>Login:</label>
            <input
              type="text"
              value={login}
              onChange={(e) => setLogin(e.target.value)}
              required
            />
          </div>
          <div>
            <label>Password:</label>
            <input
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
            />
          </div>
          <div>
            <label>Prenom:</label>
            <input
              type="text"
              value={prenom}
              onChange={(e) => setPrenom(e.target.value)}
              required
            />
          </div>
          <div>
            <label>Nom:</label>
            <input
              type="text"
              value={nom}
              onChange={(e) => setNom(e.target.value)}
              required
            />
          </div>
          <div>
            <label>Role:</label>
            <select
              value={role}
              onChange={(e) => setRole(e.target.value)}
              required
            >
              <option value="">Select a role</option>
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <button type="submit">Register</button>
        </form>
      </div>
    );
  };
  
  export default RegisterForm;