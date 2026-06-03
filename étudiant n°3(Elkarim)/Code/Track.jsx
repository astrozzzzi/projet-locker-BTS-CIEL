import { useState } from "react";

export default function Track({ goBack }) {

  const [numero, setNumero] = useState("");
  const [colis, setColis] = useState(null);
  const [erreur, setErreur] = useState("");

  const handleSearch = async () => {

    setErreur("");
    setColis(null);

    try {

      const res = await fetch(
        `http://localhost:3001/track/${numero}`
      );

      const data = await res.json();

      if (data.success === false) {
        setErreur("Aucun colis trouvé.");
      } else {
        setColis(data);
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
          Suivre un colis
        </h2>

        <label className="label">
          Numéro de colis
        </label>

        <input
          className="input"
          placeholder="482910"
          onChange={(e) => setNumero(e.target.value)}
        />

        <button
          className="button"
          onClick={handleSearch}
        >
          Rechercher
        </button>

        {erreur && (
          <div className="alertRed">
            {erreur}
          </div>
        )}

        {colis && (
          <div className="card">

            <p>
              <strong>Numéro :</strong> {colis.num_colis}
            </p>

            <p>
              <strong>Dimensions :</strong>
              {" "}
              {colis.longueur} x {colis.largeur} x {colis.hauteur} cm
            </p>

            {colis.destinataire && (
              <p>
                <strong>Destinataire :</strong>
                {" "}
                {colis.destinataire}
              </p>
            )}

          </div>
        )}

      </div>
    </div>
  );
}