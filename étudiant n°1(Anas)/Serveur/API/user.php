<?php
include("db_connect.php");
$request_method = $_SERVER["REQUEST_METHOD"];

/*GET ALL  */
function getUser()
{
    global $conn;
    $query = "SELECT * FROM Colis";
    $response = array();
    $result = mysqli_query($conn, $query);

    while($row = mysqli_fetch_assoc($result))
    {
        $response[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
}

/*GET ONE */
function getOneColis($id)
{
    global $conn;
    $query = "SELECT * FROM Colis WHERE idColis=".$id." LIMIT 1";
    $result = mysqli_query($conn, $query);
    $response = mysqli_fetch_assoc($result);

    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
}

/* ADD*/
function addColis()
{
    global $conn;

    $data = json_decode(file_get_contents("php://input"), true);

    $num = $data["num_colis"];
    $longueur = $data["longueur"];
    $largeur = $data["largeur"];
    $hauteur = $data["hauteur"];
    $livreur = $data["Livreur_idLivreur"];
    $casier = $data["Casier_idCasier"];
    $expediteur = $data["Clients_idExpediteur"];
    $destinataire = $data["Clients_idDestinataire"];

    $query = "INSERT INTO Colis 
    (num_colis, longueur, largeur, hauteur, Livreur_idLivreur, Casier_idCasier, Clients_idExpediteur, Clients_idDestinataire)
    VALUES ('$num','$longueur','$largeur','$hauteur','$livreur','$casier','$expediteur','$destinataire')";

    if(mysqli_query($conn, $query))
    {
        $response = ["status" => 1, "message" => "Colis ajouté"];
    }
    else
    {
        $response = ["status" => 0, "message" => mysqli_error($conn)];
    }

    echo json_encode($response);
}

/* ===================== UPDATE ===================== */
function updateColis($id)
{
    global $conn;

    $_PUT = array();
    parse_str(file_get_contents('php://input'), $_PUT);

    $num = $_PUT["num_colis"];
    $longueur = $_PUT["longueur"];
    $largeur = $_PUT["largeur"];
    $hauteur = $_PUT["hauteur"];

    $query = "UPDATE Colis SET 
    num_colis='$num',
    longueur='$longueur',
    largeur='$largeur',
    hauteur='$hauteur'
    WHERE idColis=".$id;

    if(mysqli_query($conn, $query))
    {
        $response = ["status" => 1, "message" => "Colis modifié"];
    }
    else
    {
        $response = ["status" => 0, "message" => mysqli_error($conn)];
    }

    echo json_encode($response);
}

/* ===================== DELETE ===================== */
function deleteColis($id)
{
    global $conn;

    $query = "DELETE FROM Colis WHERE idColis=".$id;

    if(mysqli_query($conn, $query))
    {
        $response = ["status" => 1, "message" => "Colis supprimé"];
    }
    else
    {
        $response = ["status" => 0, "message" => mysqli_error($conn)];
    }

    echo json_encode($response);
}

switch($request_method)
{
    case 'GET':
        if(!empty($_GET["id"]))
        {
            getOneColis(intval($_GET["id"]));
        }
        else
        {
            getColis();
        }
        break;

    case 'POST':
        addColis();
        break;

    case 'PUT':
        updateColis(intval($_GET["id"]));
        break;

    case 'DELETE':
        deleteColis(intval($_GET["id"]));
        break;

    default:
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
?>