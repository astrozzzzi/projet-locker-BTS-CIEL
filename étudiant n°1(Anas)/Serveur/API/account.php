<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include("db_connect.php");

$id = $_GET["id"];

// infos user
$userQuery = "
SELECT idClients, nom, prenom, email, telephone
FROM Clients
WHERE idClients='$id'
";

$userResult = mysqli_query($conn, $userQuery);
$user = mysqli_fetch_assoc($userResult);

// colis du user
$colisQuery = "
SELECT *
FROM Colis
WHERE Clients_idExpediteur='$id'
";

$colisResult = mysqli_query($conn, $colisQuery);

$colis = [];

while($row = mysqli_fetch_assoc($colisResult))
{
    $colis[] = $row;
}

echo json_encode([
    "user" => $user,
    "colis" => $colis
]);

?>