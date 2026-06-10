<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

include("db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$method = $_SERVER["REQUEST_METHOD"];


// ================= ADD COLIS =================
function addColis($data)
{
    global $conn;

    $numero = $data["num_colis"];
    $longueur = $data["longueur"];
    $largeur = $data["largeur"];
    $hauteur = $data["hauteur"];
    $destinataire = $data["destinataire"];
    $expediteur = $data["Clients_idExpediteur"];

    $query = "
    INSERT INTO Colis
    (
        num_colis,
        longueur,
        largeur,
        hauteur,
        destinataire,
        Clients_idExpediteur
    )
    VALUES
    (
        '$numero',
        '$longueur',
        '$largeur',
        '$hauteur',
        '$destinataire',
        '$expediteur'
    )
    ";

    if(mysqli_query($conn, $query))
    {
        echo json_encode([
            "success" => true,
            "message" => "Colis créé"
        ]);
    }
    else
    {
        echo json_encode([
            "success" => false,
            "message" => mysqli_error($conn)
        ]);
    }
}


// ================= TRACK =================
function trackColis($numero)
{
    global $conn;

    $query = "
    SELECT *
    FROM Colis
    WHERE num_colis='$numero'
    LIMIT 1
    ";

    $result = mysqli_query($conn, $query);

    if($row = mysqli_fetch_assoc($result))
    {
        echo json_encode($row);
    }
    else
    {
        echo json_encode([
            "success" => false
        ]);
    }
}


// ================= USER COLIS =================
function userColis($id)
{
    global $conn;

    $query = "
    SELECT *
    FROM Colis
    WHERE Clients_idExpediteur='$id'
    ";

    $result = mysqli_query($conn, $query);

    $colis = [];

    while($row = mysqli_fetch_assoc($result))
    {
        $colis[] = $row;
    }

    echo json_encode($colis);
}


// ================= ALL COLIS =================
function allColis()
{
    global $conn;

    $query = "SELECT * FROM Colis";

    $result = mysqli_query($conn, $query);

    $colis = [];

    while($row = mysqli_fetch_assoc($result))
    {
        $colis[] = $row;
    }

    echo json_encode($colis);
}


// ================= ROUTER =================
if($method === "GET")
{
    if(isset($_GET["track"]))
    {
        trackColis($_GET["track"]);
    }
    else if(isset($_GET["user"]))
    {
        userColis($_GET["user"]);
    }
    else
    {
        allColis();
    }
}
else if($method === "POST")
{
    addColis($data);
}

?>