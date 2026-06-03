import { useState } from "react";

export default function Login({
  goBack,
  setIsLoggedIn,
  setIsLivreur,
  setPage
}) {

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [erreurEmail, setErreurEmail] = useState("");

  const emailValide = (val) => {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(val);
  };

  const handleLogin = async () => {

    if (!emailValide(email)) {
      setErreurEmail("Veuillez entrer une adresse email valide");
      return;
    }

    setErreurEmail("");

    try {

      const res = await fetch("http://localhost:3001/login", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          email,
          password
        })
      });

      const data = await res.json();

      alert(data.message);

      if (data.success) {

        localStorage.setItem(
          "user",
          JSON.stringify(data.user)
        );

        localStorage.setItem(
          "type",
          data.type
        );

        setIsLoggedIn(true);

        if (data.type === "livreur") {
          setIsLivreur(true);
        }

        setPage("home");
      }

    } catch (err) {
      console.error(err);
    }
  };

  return (
    <div className="page">

      <div className="container">

        <button
          className="backBtn"
          onClick={goBack}
        >
          ← Retour
        </button>

        <h2 className="pageTitle">
          Connexion
        </h2>

        <label className="label">
          Email
        </label>

        <input
          className={erreurEmail ? "inputErreur" : "input"}
          type="email"
          placeholder="exemple@mail.com"
          value={email}
          onChange={(e) => {
            setEmail(e.target.value);
            setErreurEmail("");
          }}
        />

        {erreurEmail && (
          <p className="msgErreur">
            {erreurEmail}
          </p>
        )}

        <label className="label">
          Mot de passe
        </label>

        <input
          className="input"
          type="password"
          placeholder="••••••••"
          onChange={(e) => setPassword(e.target.value)}
        />

        <button
          className="button"
          onClick={handleLogin}
        >
          Se connecter
        </button>

      </div>
    </div>
  );
}