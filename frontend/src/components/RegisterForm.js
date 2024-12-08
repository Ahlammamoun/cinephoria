import React, { useState } from "react";
import axios from "axios";


const RegisterForm = () => {
  const [login, setLogin] = useState("");
  const [password, setPassword] = useState("");
  const [prenom, setPrenom] = useState("");
  const [nom, setNom] = useState("");
  const [role, setRole] = useState("");
  const [error, setError] = useState(null);
  const [success, setSuccess] = useState(false);

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
