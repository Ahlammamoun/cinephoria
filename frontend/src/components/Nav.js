import React, { useState, useContext } from "react";
import { UserContext } from "./UserContext";



const Navbar = () => {
    const { user, logout } = useContext(UserContext);
    const [isOpen, setIsOpen] = useState(false); // État pour le menu burger

    // Basculer le menu burger
    const toggleMenu = () => {
        setIsOpen(!isOpen);
    };

    return (
        <nav className="nav-container">
            {/* Logo */}
            <div className="logo">
                <h1>
                    <a className="logo" href="/">Cinéphoria</a>
                </h1>
            </div>

            {/* Menu burger */}
            <div className="burger-menu" onClick={toggleMenu}>
                <div className="burger-bar"></div>
                <div className="burger-bar"></div>
                <div className="burger-bar"></div>
            </div>

            {/* Liens de navigation pour Desktop */}
            <ul className="nav-links">
                {user ? (
                    <>
                    <li className="name">👤</li>
                        <li> {user.nom} {user.prenom}</li>
                        <li>
                            <a href="#" className="logout-link" onClick={logout}>Logout</a>
                        </li>
                    </>
                ) : (
                    <li><a href="/login">Login</a></li>
                )}
                <li><a href="/">Home</a></li>
                <li><a href="/reservation">Réservation</a></li>
                {user && (
                    <li><a href="/commandes">Espaces</a></li>
                )}
                <li><a href="/movies">Movies</a></li>
                <li><a href="/contact">Contact</a></li>

                {/* Liens Admin dans Desktop */}
                {user && user.role === "admin" && (
                    <li>
                        <select className="admin"
                            onChange={(e) => {
                                if (e.target.value) window.location.href = e.target.value;
                            }}
                        >
                            <option value="">Admin Menu</option>
                            <option value="/addFilm">Les Films➕</option>
                            <option value="/selectFilmToEdit">Les Films ✏️ / 🗑️</option>
                            <option value="/salleList">Les Salles ✏️</option>
                            <option value="/register">Création de compte ➕</option>
                            <option value="/dashboard">Dashboard 📊</option>
                        </select>
                    </li>
                )}
            </ul>

            {/* Menu burger (réduit pour les petits écrans) */}
            <ul className={`burger-menu-links ${isOpen ? "active" : ""}`}>
                <li><a href="/">Home</a></li>
                <li><a href="/reservation">Réservation</a></li>
                <li><a href="/seancesMobile">Séances</a></li> {/* "Séances" dans le menu burger */}
                <li><a href="/movies">Movies</a></li>
                <li><a href="/contact">Contact</a></li>
                {user && user.role === "admin" && (
                    <>
                        <li><a href="/addFilm">Ajouter un Film</a></li>
                        <li><a href="/selectFilmToEdit">Modifier/Supprimer un Film</a></li>
                        <li><a href="/salleList">Salle</a></li>
                        <li><a href="/register">Création de compte</a></li>
                        <li><a href="/dashboard">Dashboard</a></li>
                    </>
                )}
            </ul>
        </nav>
    );
};

export default Navbar;




