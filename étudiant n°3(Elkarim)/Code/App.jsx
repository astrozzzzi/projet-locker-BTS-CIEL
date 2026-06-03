import { useState } from "react";
import "./styles.css";

import Login from "./pages/Login";
import Register from "./pages/Register";
import Track from "./pages/Track";
import CreateColis from "./pages/CreateColis";
import Account from "./pages/Account";
import LivreurPage from "./pages/LivreurPage";

export default function App() {

  const [page, setPage] = useState("home");

  const [isLoggedIn, setIsLoggedIn] = useState(
    localStorage.getItem("user") ? true : false
  );

  const [isLivreur, setIsLivreur] = useState(
    localStorage.getItem("type") === "livreur"
  );

  // ---------------- HOME ----------------
  if (page === "home") {
    return (
      <div className="page">

        <div className="container">

          <h1 className="title">📦 Locker</h1>

          <p className="subtitle">
            Gérez vos colis facilement
          </p>

          {!isLoggedIn && (
            <>
              <button
                className="button"
                onClick={() => setPage("login")}
              >
                Se connecter
              </button>

              <button
                className="buttonOutline"
                onClick={() => setPage("register")}
              >
                Créer un compte
              </button>
            </>
          )}

          <button
            className="buttonOutline"
            onClick={() => setPage("track")}
          >
            🔍 Suivre un colis
          </button>

          {isLoggedIn && (
            <>
              <button
                className="button"
                onClick={() => setPage("createColis")}
              >
                + Créer un colis
              </button>

              <button
                className="buttonOutline"
                onClick={() => setPage("account")}
              >
                Mon compte
              </button>

              <button
                className="buttonRed"
                onClick={() => {
                  localStorage.removeItem("user");
                  localStorage.removeItem("type");

                  setIsLoggedIn(false);
                  setIsLivreur(false);

                  alert("Déconnecté");
                }}
              >
                Déconnexion
              </button>
            </>
          )}

          {isLivreur && (
            <button
              className="buttonGreen"
              onClick={() => setPage("livreur")}
            >
              🚚 Espace Livreur
            </button>
          )}

        </div>
      </div>
    );
  }

  // ---------------- PAGES ----------------

  if (page === "login") {
    return (
      <Login
        goBack={() => setPage("home")}
        setIsLoggedIn={setIsLoggedIn}
        setIsLivreur={setIsLivreur}
        setPage={setPage}
      />
    );
  }

  if (page === "register") {
    return (
      <Register
        goBack={() => setPage("home")}
      />
    );
  }

  if (page === "track") {
    return (
      <Track
        goBack={() => setPage("home")}
      />
    );
  }

  if (page === "createColis") {
    return (
      <CreateColis
        goBack={() => setPage("home")}
      />
    );
  }

  if (page === "account") {
    return (
      <Account
        goBack={() => setPage("home")}
      />
    );
  }

  if (page === "livreur") {
    return (
      <LivreurPage
        goBack={() => setPage("home")}
      />
    );
  }
}