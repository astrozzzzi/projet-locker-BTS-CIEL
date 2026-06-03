import { useState, useEffect } from "react";

export default function Account({ goBack }) {

  const [user, setUser] = useState(null);
  const [colis, setColis] = useState([]);

  useEffect(() => {

    const u = JSON.parse(localStorage.getItem("user") || "{}");
    setUser(u);

    // récupérer les colis du user
    fetch(`http://localhost:3001/colis-user/${u.id}`)
      .then(res => res.json())
      .then(data => setColis(data))
      .catch(err => console.error(err));

  }, []);

  return (
    <div className="page">

      <div className="container">

        <button className="backBtn" onClick={goBack}>
          ← Retour
        </button>

        <h2 className="pageTitle">
          Mon compte
        </h2>

        {user && (
          <div className="card">
            <p><strong>Nom :</strong> {user.nom}</p>
            <p><strong>Prénom :</strong> {user.prenom}</p>
            <p><strong>Email :</strong> {user.email}</p>
            <p><strong>Téléphone :</strong> {user.telephone}</p>
          </div>
        )}

        <h3 style={{ marginTop: 20 }}>
          Mes colis
        </h3>

        {colis.length === 0 ? (
          <p>Aucun colis</p>
        ) : (
          colis.map((c, i) => (
            <div key={i} className="card">
              <p><strong>N° :</strong> {c.num_colis}</p>
              <p>{c.longueur} x {c.largeur} x {c.hauteur}</p>
            </div>
          ))
        )}

      </div>
    </div>
  );
}