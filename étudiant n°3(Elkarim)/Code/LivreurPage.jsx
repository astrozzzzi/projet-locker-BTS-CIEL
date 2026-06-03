import { useEffect, useState } from "react";

export default function LivreurPage({ goBack }) {

  const [colis, setColis] = useState([]);
  const user = JSON.parse(localStorage.getItem("user") || "{}");

  useEffect(() => {

    fetch("http://localhost:3001/all-colis")
      .then(res => res.json())
      .then(data => setColis(data))
      .catch(err => console.error(err));

  }, []);

  const updateDispo = async () => {

    await fetch("http://localhost:3001/update-dispo", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        idLivreur: user.id,
        disponibilite: "Disponible"
      })
    });

    alert("OK");
  };

  return (
    <div className="page">

      <div className="container">

        <button className="backBtn" onClick={goBack}>
          ← Retour
        </button>

        <h2 className="pageTitle">
          🚚 Livreur
        </h2>

        <button className="buttonGreen" onClick={updateDispo}>
          Me rendre disponible
        </button>

        <h3>Colis disponibles</h3>

        {colis.map((c, i) => (
          <div key={i} className="card">
            <p><strong>{c.num_colis}</strong></p>
            <p>{c.longueur} x {c.largeur} x {c.hauteur}</p>
          </div>
        ))}

      </div>
    </div>
  );
}