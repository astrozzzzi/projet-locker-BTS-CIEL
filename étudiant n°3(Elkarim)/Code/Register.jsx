import { useState } from "react";

export default function Register({ goBack }) {

  const [nom, setNom] = useState("");
  const [prenom, setPrenom] = useState("");
  const [email, setEmail] = useState("");
  const [telephone, setTelephone] = useState("");
  const [password, setPassword] = useState("");

  const [erreurs, setErreurs] = useState({});

  const emailValide = (val) => {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(val);
  };

  const handleRegister = async () => {

    const nouvErreurs = {};

    if (!nom.trim())
      nouvErreurs.nom = "Le nom est obligatoire";

    if (!prenom.trim())
      nouvErreurs.prenom = "Le prénom est obligatoire";

    if (!email.trim())
      nouvErreurs.email = "L'email est obligatoire";
    else if (!emailValide(email))
      nouvErreurs.email = "Email invalide";

    if (!telephone.trim())
      nouvErreurs.telephone = "Téléphone obligatoire";
    else if (telephone.length !== 10)
      nouvErreurs.telephone = "Le numéro doit contenir 10 chiffres";

    if (!password.trim())
      nouvErreurs.password = "Mot de passe obligatoire";

    setErreurs(nouvErreurs);

    if (Object.keys(nouvErreurs).length > 0)
      return;

    try {

      const res = await fetch("http://localhost:3001/register", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          nom,
          prenom,
          email,
          telephone,
          password
        })
      });

      const data = await res.json();

      alert(data.message);

    } catch (err) {
      console.error(err);
    }
  };

  const handleTelephone = (e) => {

    const val = e.target.value.replace(/\D/g, "");

    if (val.length <= 10) {
      setTelephone(val);

      setErreurs((prev) => ({
        ...prev,
        telephone: ""
      }));
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
          Créer un compte
        </h2>

        <div className="row">

          <div className="halfField">

            <label className="label">
              Nom *
            </label>

            <input
              className={erreurs.nom ? "inputErreur" : "input"}
              placeholder="Dupont"
              value={nom}
              onChange={(e) => {
                setNom(e.target.value);

                setErreurs((p) => ({
                  ...p,
                  nom: ""
                }));
              }}
            />

            {erreurs.nom && (
              <p className="msgErreur">
                {erreurs.nom}
              </p>
            )}

          </div>

          <div className="halfField">

            <label className="label">
              Prénom *
            </label>

            <input
              className={erreurs.prenom ? "inputErreur" : "input"}
              placeholder="Lucas"
              value={prenom}
              onChange={(e) => {
                setPrenom(e.target.value);

                setErreurs((p) => ({
                  ...p,
                  prenom: ""
                }));
              }}
            />

            {erreurs.prenom && (
              <p className="msgErreur">
                {erreurs.prenom}
              </p>
            )}

          </div>

        </div>

        <label className="label">
          Email *
        </label>

        <input
          className={erreurs.email ? "inputErreur" : "input"}
          type="email"
          placeholder="nom@gmail.com"
          value={email}
          onChange={(e) => {
            setEmail(e.target.value);

            setErreurs((p) => ({
              ...p,
              email: ""
            }));
          }}
        />

        {erreurs.email && (
          <p className="msgErreur">
            {erreurs.email}
          </p>
        )}

        <label className="label">
          Téléphone *
        </label>

        <input
          className={erreurs.telephone ? "inputErreur" : "input"}
          placeholder="0600000000"
          value={telephone}
          onChange={handleTelephone}
          maxLength={10}
        />

        {erreurs.telephone && (
          <p className="msgErreur">
            {erreurs.telephone}
          </p>
        )}

        <label className="label">
          Mot de passe *
        </label>

        <input
          className={erreurs.password ? "inputErreur" : "input"}
          type="password"
          placeholder="••••••••"
          value={password}
          onChange={(e) => {
            setPassword(e.target.value);

            setErreurs((p) => ({
              ...p,
              password: ""
            }));
          }}
        />

        {erreurs.password && (
          <p className="msgErreur">
            {erreurs.password}
          </p>
        )}

        <button
          className="button"
          onClick={handleRegister}
        >
          Créer le compte
        </button>

      </div>
    </div>
  );
}