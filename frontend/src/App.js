import React from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import './App.css';
import RegisterForm from "./components/RegisterForm";
import Home from "./components/Home";
import Nav from "./components/Nav";
import Footer from "./components/Footer";
import LoginForm from "./components/LoginForm";
import { UserProvider } from "./components/UserContext";
import ReservationForm from "./components/ReservationForm";
import Commandes from "./components/Commandes";

function App() {
  return (
    <UserProvider>
    <Router>
      <div className="App">
        <header className="App-header">
          <nav>
          <Nav />
          </nav>
        </header>
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/register" element={<RegisterForm />} />
          <Route path="/login" element={<LoginForm />} />
          <Route path="/reservation" element={<ReservationForm />} />
          <Route path="/commandes" element={<Commandes />} />
        </Routes>
        <footer>
          <Footer />
        </footer>
      </div>
    </Router>
    </UserProvider>
  );
}

export default App;

