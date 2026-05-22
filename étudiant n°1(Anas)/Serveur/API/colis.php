<?php

include("db_connect.php");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");

$request_method = $_SERVER["REQUEST_METHOD"];

// ===================== GET ALL =====================
function getColis()
{
    global $conn;

    $query = "

    SELECT

    Colis.*,

    Clients.nom AS nomDestinataire,
    Clients.prenom AS prenomDestinataire,

    Locker.adresse,

    Casier.num_casier

    FROM Colis

    JOIN Clients
    ON Colis.Clients_idDestinataire = Clients.idClients

    JOIN Casier
    ON Colis.Casier_idCasier = Casier.idCasier

    JOIN Locker
    ON Casier.Locker_idLocker = Locker.idLocker

    ";

    $response = [];

    $result = mysqli_query($conn, $query);

    while($row = mysqli_fetch_assoc($result))
    {
        $response[] = $row;
    }

    echo json_encode($response);
}

// ===================== GET ONE =====================
function getOneColis($id)
{
    global $conn;

    $query = "
    SELECT *
    FROM Colis
    WHERE num_colis='$id'
    LIMIT 1
    ";

    $result = mysqli_query($conn, $query);

    $response = mysqli_fetch_assoc($result);

    echo json_encode($response);
}

// ===================== ADD =====================
function addColis()
{
    global $conn;

    $data = json_decode(file_get_contents("php://input"), true);

    $num = $data["num_colis"];
    $longueur = $data["longueur"];
    $largeur = $data["largeur"];
    $hauteur = $data["hauteur"];

    $casier = $data["Casier_idCasier"];

    $expediteur = $data["Clients_idExpediteur"];
    $destinataire = $data["Clients_idDestinataire"];

    // livreur dispo
    $livreurQuery = "
    SELECT *
    FROM Livreur
    WHERE disponibilite = 1
    LIMIT 1
    ";

    $livreurResult = mysqli_query($conn, $livreurQuery);

    $livreur = mysqli_fetch_assoc($livreurResult);

    if(!$livreur)
    {
        echo json_encode([
            "status" => 0,
            "message" => "Aucun livreur disponible"
        ]);

        return;
    }

    $idLivreur = $livreur["idLivreur"];

    // Génère ID colis
    $idQuery = "SELECT MAX(idColis) as maxId FROM Colis";

    $idResult = mysqli_query($conn, $idQuery);

    $row = mysqli_fetch_assoc($idResult);

    $newId = $row["maxId"] + 1;

    $query = "

    INSERT INTO Colis
    (
        idColis,
        num_colis,
        longueur,
        largeur,
        hauteur,
        Livreur_idLivreur,
        Casier_idCasier,
        Clients_idExpediteur,
        Clients_idDestinataire
    )

    VALUES
    (
        '$newId',
        '$num',
        '$longueur',
        '$largeur',
        '$hauteur',
        '$idLivreur',
        '$casier',
        '$expediteur',
        '$destinataire'
    )

    ";

    if(mysqli_query($conn, $query))
    {
        echo json_encode([
            "status" => 1,
            "message" => "Colis créé"
        ]);
    }
    else
    {
        echo json_encode([
            "status" => 0,
            "message" => mysqli_error($conn)
        ]);
    }
}

// ===================== ROUTER =====================
switch($request_method)
{
    case 'GET':

        if(!empty($_GET["id"]))
        {
            getOneColis($_GET["id"]);
        }
        else
        {
            getColis();
        }

        break;

    case 'POST':

        addColis();

        break;

    default:

        header("HTTP/1.0 405 Method Not Allowed");

        break;
}

?>