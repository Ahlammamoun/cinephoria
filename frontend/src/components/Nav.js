// components/Nav.js
import React, { useContext } from "react";
import { UserContext } from "./UserContext";


function Nav() {
  const { user, logout } = useContext(UserContext);
  console.log(user);
  return (
    <nav className="nav-container">
      <div className="logo">
        <h1><a className="logo" href="/">Cinéphoria</a></h1>

      </div>
      <ul className="nav-links">
        {user ? (
          <>
            <li>Welcome,  {user.nom} ({user.email})!</li>
            <button className="logout" onClick={logout}>Logout</button>
          </>
        ) : (
          <li><a href="/login">Login</a></li>
        )}
        <li><a href="/">Home</a></li>
        <li><a href="/reservation">Réservation</a></li>
        {user && ( // Afficher uniquement si l'utilisateur est connecté
          <li>
            <a href="/commandes">Commandes</a>
          </li>
        )}
        <li><a href="/movies">Movies</a></li>
        <li><a href="/contact">Contact</a></li>
      </ul>
    </nav>
  );
}

export default Nav;
