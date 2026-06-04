<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include("db_connect.php"); 

$numero = $_GET["numero"];

$query = "
SELECT *
FROM Colis
WHERE num_colis='$numero'
";

$result = mysqli_query($conn, $query);

if($row = mysqli_fetch_assoc($result))
{
    echo json_encode($row);
}
else
{
    echo json_encode([
        "success" => false,
        "message" => "Colis introuvable"
    ]);
}

?>