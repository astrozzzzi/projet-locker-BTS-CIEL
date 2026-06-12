<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
header("Content-Type: application/json");

include("db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$method = $_SERVER["REQUEST_METHOD"];

function jsonResponse($data) {
    echo json_encode($data);
    exit;
}

/* ================= CREATE COLIS ================= */
function addColis($data)
{
    global $conn;

    if (!$data) {
        jsonResponse(["success"=>false,"message"=>"Données manquantes"]);
    }

    $numero = $data["num_colis"] ?? null;
    $longueur = $data["longueur"] ?? null;
    $largeur = $data["largeur"] ?? null;
    $hauteur = $data["hauteur"] ?? null;
    $destinataire = $data["destinataire"] ?? "";
    $expediteur = $data["Clients_idExpediteur"] ?? null;

    if (!$numero || !$expediteur) {
        jsonResponse(["success"=>false,"message"=>"Paramètre manquant"]);
    }

    // valeurs par défaut IMPORTANTES
    $casier = 1;
    $destId = $data["Clients_idDestinataire"] ?? $expediteur;
    $livreur = "NULL";

    $query = "
        INSERT INTO Colis
        (num_colis, longueur, largeur, hauteur, destinataire,
         Livreur_idLivreur, Casier_idCasier,
         Clients_idExpediteur, Clients_idDestinataire, statut)
        VALUES
        ('$numero','$longueur','$largeur','$hauteur','$destinataire',
         $livreur,'$casier',
         '$expediteur','$destId','en_attente')
    ";

    if (mysqli_query($conn, $query)) {
        jsonResponse([
            "success"=>true,
            "message"=>"Colis créé"
        ]);
    }

    jsonResponse([
        "success"=>false,
        "message"=>mysqli_error($conn)
    ]);
}

/* ================= GET USER COLIS ================= */
function userColis($id)
{
    global $conn;

    if (!$id) {
        jsonResponse(["success"=>false,"message"=>"ID manquant"]);
    }

    $res = mysqli_query($conn,"
        SELECT * FROM Colis
        WHERE Clients_idExpediteur='$id'
        ORDER BY idColis DESC
    ");

    $colis = [];
    while($row = mysqli_fetch_assoc($res)) {
        $colis[] = $row;
    }

    jsonResponse([
        "success"=>true,
        "colis"=>$colis
    ]);
}

/* ================= ROUTER ================= */
if ($method === "GET") {

    if (isset($_GET["user"])) {
        userColis($_GET["user"]);
    } else {
        jsonResponse(["success"=>true,"colis"=>[]]);
    }

} elseif ($method === "POST") {
    addColis($data);
}

?>