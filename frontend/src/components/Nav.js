// components/Nav.js
import React, { useContext } from "react";
import { UserContext } from "./UserContext";


function Nav() {
  const { user } = useContext(UserContext);
  console.log(user);
  return (
    <nav className="nav-container">
    <div className="logo">
      <h1>Cinéphoria</h1>
    </div>
    <ul className="nav-links">
      {user ? (
        <>
          <p>Welcome,  {user.nom} ({user.role})!</p>
          <li><a href="/">Logout</a></li>
        </>
      ) : (
        <li><a href="/login">Login</a></li>
      )}
      <li><a href="/">Accueil</a></li>
      <li><a href="/reservation">Réservation</a></li>
      <li><a href="/films">Films</a></li>
      <li><a href="/contact">Contact</a></li>
    </ul>
  </nav>
  );
}

export default Nav;
