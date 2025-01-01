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
import MoviesList from "./components/MoviesList";
import AddFilm from "./components/AddFilm";
import EditFilm from "./components/EditFilm";
import SelectFilmToEdit from "./components/SelectFilmToEdit";
import SalleList from "./components/SalleList";
import Dashboard from "./components/Dashboard";
import ForgotPassword from "./components/ForgotPassword";
import ChangePassword from "./components/ChangePassword";
import SeancesMobile from "./components/SeancesMobile";
import ContactForm from "./components/ContactForm";


function App() {
  return (

    <Router>
      <UserProvider>

        <div className="App">
          <header className="App-header">
            <nav>
              <Nav />
            </nav>
          </header>
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/register" element={<RegisterForm />} />
            <Route path="/contactForm" element={<ContactForm />} />
            <Route path="/movies" element={<MoviesList />} />

            <Route path="/login" element={<LoginForm />} />
            <Route path="/reservation" element={<ReservationForm />} />
            <Route path="/commandes" element={<Commandes />} />
            <Route path="/addFilm" element={<AddFilm />} />
            <Route path="/editFilm" element={<EditFilm />} />
            <Route path="/selectFilmToEdit" element={<SelectFilmToEdit />} />
            <Route path="/salleList" element={<SalleList />} />
            <Route path="/dashboard" element={<Dashboard />} />
            <Route path="/forgot-password" element={<ForgotPassword />} />
            <Route path="/change-password" element={<ChangePassword />} />
            <Route path="/seancesMobile" element={<SeancesMobile />} />
          </Routes>
          <footer>
            <Footer />
          </footer>
        </div>

      </UserProvider>
    </Router>
  );
}

export default App;

