import React, { useState, useContext } from "react";
import axios from "axios";
import { UserContext } from "./UserContext"; // Chemin correct vers le fichier UserContext
import { useNavigate } from "react-router-dom";

const LoginForm = () => {
  const [login, setLogin] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState(null);
  const [success, setSuccess] = useState(false);

  const { setUser } = useContext(UserContext); // Utilisation correcte du contexte
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!login || !password) {
      setError("Both fields are required!");
      return;
    }

    try {
      const response = await axios.post("http://localhost:8000/api/login", {
        login,
        password,
      });
      const { token, user } = response.data;
      sessionStorage.setItem("jwtToken", token);
      setUser(user); // Mettre à jour le contexte utilisateur
      setSuccess(true);
      navigate("/"); // Redirection vers l'accueil
    } catch (err) {
      if (err.response?.status === 423) {
          // Code 423 = mot de passe temporaire, redirection vers changement de mot de passe
          navigate("/change-password");
      } else {
          setError(err.response?.data?.error || "Erreur lors de la connexion.");
      }
  }
  };

  return (
    <div className="login-container">
      <h2>Login</h2>
      {success && <p className="success">Login successful!</p>}
      {error && <p className="error">{error}</p>}
      <form onSubmit={handleSubmit}>
        <div className="form-group">
          <label className="login-input">Login:</label>
          <input
            id="login-input"
            type="text"
            value={login}
            onChange={(e) => setLogin(e.target.value)}
            required
          />
        </div>
        <div className="form-group">
          <label className="password-input">Password:</label>
          <input
            id="password-input"
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
          />
        </div>
        <button type="submit" className="styled-button">Login</button>
        <p className="register-link">
          Pas encore de compte ? <a href="/register">S'inscrire</a>
        </p>
        <button onClick={() => navigate("/forgot-password")}>
                Mot de passe oublié ?
            </button>
      </form>
    </div>
  );
};

export default LoginForm;
