// components/Nav.js
import React from "react";


function Nav() {
  return (
    <nav className="nav-container">
      <div className="logo">
        <h1>Cinéphoria</h1>
      </div>
      <ul className="nav-links">
        <li><a href="/">Accueil</a></li>
        <li><a href="/login">Se connecter</a></li>
        <li><a href="/reservation">Réservation</a></li>
        <li><a href="/films">Films</a></li>
        <li><a href="/contact">Contact</a></li>
      </ul>
    </nav>
  );
}

export default Nav;
