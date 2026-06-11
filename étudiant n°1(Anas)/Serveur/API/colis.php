<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
header("Content-Type: application/json");

include("db_connect.php");
require_once("sms.php"); // ← Notre nouveau fichier SMS

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$data   = json_decode(file_get_contents("php://input"), true);
$method = $_SERVER["REQUEST_METHOD"];


// ================= ADD COLIS =================
function addColis($data)
{
    global $conn;

    $numero      = $data["num_colis"];
    $longueur    = $data["longueur"];
    $largeur     = $data["largeur"];
    $hauteur     = $data["hauteur"];
    $destinataire = $data["destinataire"];
    $expediteur  = $data["Clients_idExpediteur"];

    $query = "
    INSERT INTO Colis
    (num_colis, longueur, largeur, hauteur, destinataire, Clients_idExpediteur)
    VALUES ('$numero','$longueur','$largeur','$hauteur','$destinataire','$expediteur')
    ";

    if (mysqli_query($conn, $query)) {

        // ── SMS à l'expéditeur : colis pris en charge ──
        envoyerSMSClient(
            $conn,
            (int)$expediteur,
            "Votre colis n°$numero a bien été enregistré et sera pris en charge par un livreur."
        );

        echo json_encode(["success" => true, "message" => "Colis créé"]);
    } else {
        echo json_encode(["success" => false, "message" => mysqli_error($conn)]);
    }
}


// ================= MISE À JOUR STATUT COLIS =================
// Appelée par le Raspberry Pi ou l'app livreur quand le colis est déposé dans le locker
// Méthode : PUT   Body JSON : { "num_colis": "123", "action": "depose" | "recupere" }
function updateStatutColis($data)
{
    global $conn;

    $numero = $data["num_colis"];
    $action = $data["action"]; // "depose" ou "recupere"

    // Récupère idColis + Clients_idDestinataire pour le SMS
    $result = mysqli_query($conn,
        "SELECT idColis, Clients_idDestinataire FROM Colis WHERE num_colis='$numero' LIMIT 1"
    );

    if (!$result || mysqli_num_rows($result) === 0) {
        echo json_encode(["success" => false, "message" => "Colis introuvable"]);
        return;
    }

    $row          = mysqli_fetch_assoc($result);
    $idDestinataire = (int)$row['Clients_idDestinataire'];

    if ($action === "depose") {
        // ── SMS au destinataire : colis disponible dans le locker ──
        envoyerSMSClient(
            $conn,
            $idDestinataire,
            "Votre colis n°$numero est disponible dans le locker. Récupérez-le avec votre code."
        );
        echo json_encode(["success" => true, "message" => "Colis marqué comme déposé, SMS envoyé"]);

    } else if ($action === "recupere") {
        // ── SMS au destinataire : confirmation récupération ──
        envoyerSMSClient(
            $conn,
            $idDestinataire,
            "Votre colis n°$numero a bien été récupéré. Merci d'utiliser notre service Locker."
        );
        echo json_encode(["success" => true, "message" => "Colis marqué comme récupéré, SMS envoyé"]);

    } else {
        echo json_encode(["success" => false, "message" => "Action inconnue"]);
    }
}


// ================= TRACK =================
function trackColis($numero)
{
    global $conn;

    $query  = "SELECT * FROM Colis WHERE num_colis='$numero' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode($row);
    } else {
        echo json_encode(["success" => false]);
    }
}


// ================= USER COLIS =================
function userColis($id)
{
    global $conn;

    $result = mysqli_query($conn, "SELECT * FROM Colis WHERE Clients_idExpediteur='$id'");
    $colis  = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $colis[] = $row;
    }

    echo json_encode($colis);
}


// ================= ALL COLIS =================
function allColis()
{
    global $conn;

    $result = mysqli_query($conn, "SELECT * FROM Colis");
    $colis  = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $colis[] = $row;
    }

    echo json_encode($colis);
}


// ================= ROUTER =================
if ($method === "GET") {
    if (isset($_GET["track"]))      trackColis($_GET["track"]);
    else if (isset($_GET["user"]))  userColis($_GET["user"]);
    else                            allColis();

} else if ($method === "POST") {
    addColis($data);

} else if ($method === "PUT") {
    // Utilisé par le Raspberry Pi / app livreur pour signaler dépôt ou récupération
    updateStatutColis($data);
}
