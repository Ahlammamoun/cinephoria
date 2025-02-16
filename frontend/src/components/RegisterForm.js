import React, { useState, useContext, useEffect } from "react";
import axios from "axios";
import { UserContext } from "./UserContext";

const RegisterForm = () => {
  const [login, setLogin] = useState("");
  const [password, setPassword] = useState("");
  const [prenom, setPrenom] = useState("");
  const [nom, setNom] = useState("");
  const [role, setRole] = useState("user"); // Définir "user" par défaut
  const [error, setError] = useState(null);
  const [success, setSuccess] = useState(false);
  const { user } = useContext(UserContext); // Contexte utilisateur

  // Vérifier si l'utilisateur est admin et ajuster le rôle
  useEffect(() => {
    if (user && user.role === "admin") {
      setRole(""); // Autoriser le choix pour admin
    } else {
      setRole("user"); // Par défaut "user"
    }
  }, [user]);

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!login || !password || !prenom || !nom || !role) {
      setError("All fields are required!");
      return;
    }

    try {
      const response = await axios.post("http://localhost:8000/api/register", {
        login,
        password,
        prenom,
        nom,
        role,
      });

      setSuccess(true);
      setError(null);
    } catch (err) {
      setError(err.response?.data?.error || "Something went wrong");
      setSuccess(false);
    }
  };

  return (
    <div className="register-container">
      <h2>Register</h2>
      {success && <p className="success">User created successfully!</p>}
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
            <option value="user">User</option>
            {user && user.role === "admin" && (
              <>
                <option value="admin">Admin</option>
                <option value="employe">Employe</option>
              </>
            )}
          </select>
        </div>
        <button type="submit">Register</button>
      </form>
    </div>
  );
};

export default RegisterForm;
