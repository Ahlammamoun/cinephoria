import React from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import './App.css';
import RegisterForm from "./components/RegisterForm";
import Home from "./components/Home";
import Nav from "./components/Nav";
import Footer from "./components/Footer";

function App() {
  return (
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
        </Routes>
        <footer>
          <Footer />
        </footer>
      </div>
    </Router>
  );
}

export default App;

