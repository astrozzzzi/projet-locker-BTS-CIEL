import { useState, useEffect, useRef } from "react";

const LOCKERS = [
  {
    id: 1,
    nom: "Locker Lycée François Mauriac",
    adresse: "Lycée François Mauriac, Andrézieux-Bouthéon",
    lat: 45.531541,
    lng: 4.272897
  }
];

export default function CreateColis({ goBack }) {

  const user = JSON.parse(localStorage.getItem("user") || "{}");

  const [etape, setEtape] = useState(1);

  // Étape 1
  const [numero] = useState(Math.floor(100000 + Math.random() * 900000));
  const [longueur, setLongueur] = useState("");
  const [largeur, setLargeur] = useState("");
  const [hauteur, setHauteur] = useState("");
  const [destinataire, setDestinataire] = useState("");
  const [erreursEtape1, setErreursEtape1] = useState({});

  // Étape 2
  const [lockerChoisi, setLockerChoisi] = useState(null);
  const [mapReady, setMapReady] = useState(false);
  const mapRef = useRef(null);
  const mapInstanceRef = useRef(null);

  // Étape 3
  const [loading, setLoading] = useState(false);
  const [resultat, setResultat] = useState(null);

  // Charger Leaflet
  useEffect(() => {
    if (document.getElementById("leaflet-css")) {
      setMapReady(true);
      return;
    }
    const link = document.createElement("link");
    link.id = "leaflet-css";
    link.rel = "stylesheet";
    link.href = "https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css";
    document.head.appendChild(link);

    const script = document.createElement("script");
    script.src = "https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js";
    script.onload = () => setMapReady(true);
    document.head.appendChild(script);
  }, []);

  // Initialiser la carte à l'étape 2
  useEffect(() => {
    if (etape !== 2 || !mapReady || !mapRef.current || mapInstanceRef.current) return;

    const L = window.L;

    delete L.Icon.Default.prototype._getIconUrl;
    L.Icon.Default.mergeOptions({
      iconUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png",
      iconRetinaUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png",
      shadowUrl: "https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png",
    });

    const map = L.map(mapRef.current).setView([45.531541, 4.272897], 15);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    LOCKERS.forEach((locker) => {
      L.marker([locker.lat, locker.lng])
        .addTo(map)
        .bindPopup(`
          <div style="font-family:Arial;min-width:150px">
            <strong>${locker.nom}</strong><br/>
            <small>${locker.adresse}</small><br/>
            <button
              onclick="window.choisirLocker(${locker.id})"
              style="margin-top:8px;background:#007bff;color:white;border:none;
                     padding:6px 12px;border-radius:6px;cursor:pointer;width:100%;font-weight:bold"
            >
              Choisir ce locker
            </button>
          </div>
        `);
    });

    mapInstanceRef.current = map;

    window.choisirLocker = (id) => {
      const found = LOCKERS.find((l) => l.id === id);
      if (found) {
        setLockerChoisi(found);
        map.closePopup();
      }
    };

    return () => { delete window.choisirLocker; };
  }, [etape, mapReady]);

  // Validation étape 1
  const validerEtape1 = () => {
    const errs = {};
    if (!longueur.trim()) errs.longueur = "Obligatoire";
    if (!largeur.trim())  errs.largeur  = "Obligatoire";
    if (!hauteur.trim())  errs.hauteur  = "Obligatoire";
    if (!destinataire.trim()) errs.destinataire = "Obligatoire";
    setErreursEtape1(errs);
    if (Object.keys(errs).length === 0) setEtape(2);
  };

  // Envoi final
  const handleCreate = async () => {
    setLoading(true);
    try {
      const res = await fetch("http://172.18.199.9/backend/colis.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          num_colis: numero,
          longueur,
          largeur,
          hauteur,
          destinataire,
          Clients_idExpediteur: user.id
        })
      });
      const data = await res.json();
      setResultat(data);
    } catch (err) {
      console.error(err);
      setResultat({ success: false, message: "Erreur de connexion" });
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="page">

      <div className="container">

        <button className="backBtn" onClick={goBack}>
          ← Retour
        </button>

        <h2 className="pageTitle">
          Créer un colis
        </h2>

        {/* Barre de progression */}
        <div style={{ display: "flex", alignItems: "center", marginBottom: 24 }}>
          {[1, 2, 3].map((n) => (
            <div key={n} style={{ display: "flex", alignItems: "center", flex: 1 }}>
              <div style={{
                width: 28, height: 28, borderRadius: "50%",
                background: etape >= n ? "#007bff" : "#e0e0e0",
                color: etape >= n ? "white" : "#999",
                display: "flex", alignItems: "center", justifyContent: "center",
                fontWeight: "bold", fontSize: 13, flexShrink: 0
              }}>
                {n}
              </div>
              {n < 3 && (
                <div style={{
                  flex: 1, height: 3,
                  background: etape > n ? "#007bff" : "#e0e0e0"
                }} />
              )}
            </div>
          ))}
        </div>

        {/* ── ÉTAPE 1 : Dimensions ── */}
        {etape === 1 && (
          <div>

            <p style={{ color: "#888", fontSize: 13, marginBottom: 16 }}>
              Numéro de colis : <strong>#{numero}</strong>
            </p>

            <label className="label">Longueur (cm)</label>
            <input
              className={erreursEtape1.longueur ? "inputErreur" : "input"}
              placeholder="30"
              value={longueur}
              onChange={(e) => {
                setLongueur(e.target.value);
                setErreursEtape1(p => ({ ...p, longueur: "" }));
              }}
            />
            {erreursEtape1.longueur && <p className="msgErreur">{erreursEtape1.longueur}</p>}

            <label className="label">Largeur (cm)</label>
            <input
              className={erreursEtape1.largeur ? "inputErreur" : "input"}
              placeholder="20"
              value={largeur}
              onChange={(e) => {
                setLargeur(e.target.value);
                setErreursEtape1(p => ({ ...p, largeur: "" }));
              }}
            />
            {erreursEtape1.largeur && <p className="msgErreur">{erreursEtape1.largeur}</p>}

            <label className="label">Hauteur (cm)</label>
            <input
              className={erreursEtape1.hauteur ? "inputErreur" : "input"}
              placeholder="10"
              value={hauteur}
              onChange={(e) => {
                setHauteur(e.target.value);
                setErreursEtape1(p => ({ ...p, hauteur: "" }));
              }}
            />
            {erreursEtape1.hauteur && <p className="msgErreur">{erreursEtape1.hauteur}</p>}

            <label className="label">Destinataire</label>
            <input
              className={erreursEtape1.destinataire ? "inputErreur" : "input"}
              placeholder="Nom complet du destinataire"
              value={destinataire}
              onChange={(e) => {
                setDestinataire(e.target.value);
                setErreursEtape1(p => ({ ...p, destinataire: "" }));
              }}
            />
            {erreursEtape1.destinataire && <p className="msgErreur">{erreursEtape1.destinataire}</p>}

            <button className="button" onClick={validerEtape1}>
              Suivant →
            </button>

          </div>
        )}

        {/* ── ÉTAPE 2 : Carte ── */}
        {etape === 2 && (
          <div>

            <label className="label">Choisissez un locker</label>

            {!mapReady && (
              <p style={{ color: "#999", fontSize: 13 }}>
                Chargement de la carte...
              </p>
            )}

            <div
              ref={mapRef}
              style={{
                width: "100%",
                height: 280,
                borderRadius: 8,
                border: "1px solid #ddd",
                marginBottom: 14,
                opacity: mapReady ? 1 : 0,
                transition: "opacity 0.3s"
              }}
            />

            {lockerChoisi ? (
              <div className="card" style={{ marginTop: 0, marginBottom: 14, display: "flex", justifyContent: "space-between", alignItems: "center" }}>
                <span>
                  📍 <strong>{lockerChoisi.nom}</strong><br />
                  <small style={{ color: "#666" }}>{lockerChoisi.adresse}</small>
                </span>
                <button
                  onClick={() => setLockerChoisi(null)}
                  style={{ background: "none", border: "none", cursor: "pointer", color: "#aaa", fontSize: 16 }}
                >
                  ✕
                </button>
              </div>
            ) : (
              <p style={{ color: "#999", fontSize: 13, textAlign: "center", marginBottom: 14 }}>
                Cliquez sur un marqueur pour sélectionner un locker
              </p>
            )}

            <div className="row">
              <button className="buttonOutline" onClick={() => setEtape(1)} style={{ flex: 1 }}>
                ← Précédent
              </button>
              <button
                className={lockerChoisi ? "button" : "buttonDisabled"}
                onClick={() => { if (lockerChoisi) setEtape(3); }}
                style={{ flex: 1 }}
              >
                Suivant →
              </button>
            </div>

          </div>
        )}

        {/* ── ÉTAPE 3 : Confirmation ── */}
        {etape === 3 && !resultat && (
          <div>

            <p style={{ color: "#555", fontSize: 14, marginBottom: 16 }}>
              Vérifiez les informations avant de confirmer.
            </p>

            <div className="card">
              <p><strong>N° colis :</strong> {numero}</p>
              <p><strong>Dimensions :</strong> {longueur} × {largeur} × {hauteur} cm</p>
              <p><strong>Destinataire :</strong> {destinataire}</p>
              <p><strong>Locker :</strong> {lockerChoisi?.nom}</p>
            </div>

            <p style={{ fontSize: 12, color: "#888", marginTop: 12, marginBottom: 16 }}>
              Un livreur disponible sera automatiquement attribué.
            </p>

            <div className="row">
              <button className="buttonOutline" onClick={() => setEtape(2)} style={{ flex: 1 }}>
                ← Précédent
              </button>
              <button
                className={loading ? "buttonDisabled" : "button"}
                onClick={handleCreate}
                disabled={loading}
                style={{ flex: 1 }}
              >
                {loading ? "Envoi..." : "✓ Confirmer"}
              </button>
            </div>

          </div>
        )}

        {/* ── RÉSULTAT ── */}
        {resultat && (
          <div>

            {resultat.success ? (
              <div className="card" style={{ textAlign: "center", background: "#d4edda", border: "1px solid #c3e6cb", color: "#155724" }}>
                <p style={{ fontSize: 22, marginBottom: 8 }}>✅</p>
                <p><strong>{resultat.message}</strong></p>
                <p style={{ fontSize: 13, marginTop: 6 }}>
                  Un livreur a été attribué à votre colis.
                </p>
              </div>
            ) : (
              <div className="alertRed">
                ❌ {resultat.message}
              </div>
            )}

            <button className="buttonOutline" onClick={goBack} style={{ marginTop: 16 }}>
              Retour à l'accueil
            </button>

          </div>
        )}

      </div>
    </div>
  );
}