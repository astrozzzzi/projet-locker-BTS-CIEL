<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include("db_connect.php");

// Sécurisation de l'id
$id = $_GET["id"] ?? null;

if (!$id) {
    echo json_encode([
        "success" => false,
        "message" => "ID manquant"
    ]);
    exit;
}

$id = mysqli_real_escape_string($conn, $id);

// --------------------
// USER
// --------------------
$userQuery = "
SELECT idClients, nom, prenom, email, telephone
FROM Clients
WHERE idClients='$id'
";

$userResult = mysqli_query($conn, $userQuery);
$user = mysqli_fetch_assoc($userResult);

// --------------------
// COLIS (EXPEDITEUR + DESTINATAIRE)
// --------------------
$colisQuery = "
SELECT *
FROM Colis
WHERE Clients_idExpediteur='$id'
   OR Clients_idDestinataire='$id'
ORDER BY idColis DESC
";

$colisResult = mysqli_query($conn, $colisQuery);

$colis = [];
if ($colisResult) {
    while ($row = mysqli_fetch_assoc($colisResult)) {
        $colis[] = $row;
    }
}

// --------------------
// RESPONSE
// --------------------
echo json_encode([
    "success" => true,
    "user" => $user,
    "colis" => $colis
]);

?>