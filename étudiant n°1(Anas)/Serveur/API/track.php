<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include("db_connect.php");

$numero = $_GET["numero"] ?? null;

if (!$numero) {
    echo json_encode([
        "success" => false,
        "message" => "Numéro de colis manquant"
    ]);
    exit;
}

$query = "
SELECT * FROM Colis
WHERE num_colis='$numero'
LIMIT 1
";

$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode([
        "success" => true,
        "colis" => $row
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Colis introuvable"
    ]);
}

?>