import React, { useContext } from "react";
import { UserContext } from "./UserContext";

function Nav() {
  const { user, logout } = useContext(UserContext); // Utilisation du UserContext
  console.log(user);

  return (
    <nav className="nav-container">
      <div className="logo">
        <h1><a className="logo" href="/">Cinéphoria</a></h1>
      </div>
      <ul className="nav-links">
        {user ? (
          <>
            {/* Affichage du nom et email de l'utilisateur */}
            <li>{user.nom}</li>
            <li><button className="logout" onClick={logout}>Logout</button></li>
          </>
        ) : (
          <li><a href="/login">Login</a></li>
        )}
        <li><a href="/">Home</a></li>
        <li><a href="/reservation">Réservation</a></li>
        {user && ( // Afficher uniquement si l'utilisateur est connecté
          <li>
            <a href="/commandes">Espaces</a>
          </li>
        )}
        <li><a href="/movies">Movies</a></li>
        
        {/* Vérification si l'utilisateur est admin */}
        {user && user.role === "admin" && (
          <li>
            <select className="admin"
              onChange={(e) => {
                if (e.target.value) window.location.href = e.target.value; // Redirection
              }}
            >
              <option value="">Admin Menu</option>
              <option value="/addFilm">Ajouter un Film</option>
              <option value="/selectFilmToEdit">Modifier/Supprimer un Film</option>
              <option value="/salleList">Salle</option>
            </select>
          </li>
        )}
        
        <li><a href="/contact">Contact</a></li>
      </ul>
    </nav>
  );
}

export default Nav;

