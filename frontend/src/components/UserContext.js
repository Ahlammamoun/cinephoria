import React, { createContext, useState, useEffect } from "react";
import axios from "axios";


export const UserContext = createContext();

export const UserProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  


  useEffect(() => {
    const checkSession = async () => {
      try {
        const response = await axios.get("http://localhost:8000/api/check-session", {
          withCredentials: true, // Nécessaire pour transmettre les sessions
        });
        if (response.data.authenticated) {
          setUser(response.data.user); // Mettre à jour l'état utilisateur
        } else {
          setUser(null);
        }
      } catch (error) {
        console.error("Erreur lors de la vérification de la session :", error);
        setUser(null);
      } finally {
        setLoading(false); // Marque la fin du chargement
      }
    };

    checkSession();
  }, []);


  const logout = async () => {
    try {
      await axios.post("http://localhost:8000/api/logout", {}, { withCredentials: true });
      setUser(null); // Supprime l'état utilisateur

    } catch (error) {
      console.error("Erreur lors de la déconnexion :", error);
    }
  };



  return (
    <UserContext.Provider value={{ user, setUser, loading, logout }}>
      {children}
    </UserContext.Provider>
  );
};
