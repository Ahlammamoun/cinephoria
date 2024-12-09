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
      localStorage.setItem("jwtToken", token);
      setUser(user); // Mettre à jour le contexte utilisateur
      setSuccess(true);
      navigate("/"); // Redirection vers l'accueil
    } catch (err) {
      setError(err.response?.data?.error || "Login failed. Please try again.");
      setSuccess(false);
    }
  };

  return (
    <div className="login-container">
      <h2>Login</h2>
      {success && <p className="success">Login successful!</p>}
      {error && <p className="error">{error}</p>}
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
        <button type="submit">Login</button>
        <li><a href="/register">Register</a></li>
      </form>
    </div>
  );
};

export default LoginForm;
