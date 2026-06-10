<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

include("db_connect.php");
require_once("mail.php");

$request_method = $_SERVER["REQUEST_METHOD"];
$data = json_decode(file_get_contents("php://input"), true);

//////////////////////////////////////////////////////
// GET USERS (CLIENTS)
//////////////////////////////////////////////////////
function getUsers()
{
    global $conn;

    $clients = [];
    $livreurs = [];

    $res1 = mysqli_query($conn, "SELECT idClients, nom, prenom, email, telephone FROM Clients");
    while($row = mysqli_fetch_assoc($res1)) {
        $clients[] = $row;
    }

    $res2 = mysqli_query($conn, "SELECT idLivreur, nom, prenom, email, telephone, disponibilite FROM Livreur");
    while($row = mysqli_fetch_assoc($res2)) {
        $livreurs[] = $row;
    }

    echo json_encode([
        "clients" => $clients,
        "livreurs" => $livreurs
    ]);
}

//////////////////////////////////////////////////////
// REGISTER CLIENT
//////////////////////////////////////////////////////
function addUser($data)
{
    global $conn;

    $nom = $data["nom"];
    $prenom = $data["prenom"];
    $email = $data["email"];
    $telephone = $data["telephone"];
    $password = $data["password"];

    $check = "SELECT idClients FROM Clients WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $check);

    if (mysqli_num_rows($result) > 0) {
        echo json_encode(["success" => false, "message" => "Email déjà utilisé"]);
        return;
    }

    $idQuery = "SELECT MAX(idClients) as maxId FROM Clients";
    $idResult = mysqli_query($conn, $idQuery);
    $row = mysqli_fetch_assoc($idResult);

    $newId = ($row["maxId"] ?? 0) + 1;

    $query = "
        INSERT INTO Clients (idClients, nom, prenom, telephone, mdp, email)
        VALUES ('$newId','$nom','$prenom','$telephone','$password','$email')
    ";

    if (mysqli_query($conn, $query)) {
        echo json_encode(["success" => true, "message" => "Compte créé"]);
    } else {
        echo json_encode(["success" => false, "message" => mysqli_error($conn)]);
    }
}

//////////////////////////////////////////////////////
// REGISTER LIVREUR
//////////////////////////////////////////////////////
function addLivreur($data)
{
    global $conn;

    $nom = $data["nom"];
    $prenom = $data["prenom"];
    $email = $data["email"];
    $telephone = $data["telephone"];
    $password = $data["password"];

    $check = "SELECT idLivreur FROM Livreur WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $check);

    if (mysqli_num_rows($result) > 0) {
        echo json_encode(["success" => false, "message" => "Email déjà utilisé"]);
        return;
    }

    $idQuery = "SELECT MAX(idLivreur) as maxId FROM Livreur";
    $idResult = mysqli_query($conn, $idQuery);
    $row = mysqli_fetch_assoc($idResult);

    $newId = ($row["maxId"] ?? 0) + 1;

    $query = "
        INSERT INTO Livreur (idLivreur, nom, prenom, telephone, mdp, email, disponibilite)
        VALUES ('$newId','$nom','$prenom','$telephone','$password','$email','0')
    ";

    if (mysqli_query($conn, $query)) {
        echo json_encode(["success" => true, "message" => "Compte livreur créé"]);
    } else {
        echo json_encode(["success" => false, "message" => mysqli_error($conn)]);
    }
}

//////////////////////////////////////////////////////
// LOGIN CLIENT + 2FA STEP 1
//////////////////////////////////////////////////////
function loginUser($data)
{
    global $conn;

    $email = $data["email"];
    $password = $data["password"];

    $query = "SELECT * FROM Clients WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($user = mysqli_fetch_assoc($result)) {

        if ($user["mdp"] === $password) {

            // 👉 génération code 2FA
            $code = rand(100000, 999999);
            $expiration = date("Y-m-d H:i:s", time() + 300);

            mysqli_query($conn, "
                UPDATE Clients 
                SET otp_code='$code', otp_expiration='$expiration'
                WHERE email='$email'
            ");

            sendOTPEmail($email, $code);

            echo json_encode([
                "success" => true,
                "step" => "2fa_required",
                "message" => "Code envoyé par email"
            ]);

        } else {
            echo json_encode(["success" => false, "message" => "Mot de passe incorrect"]);
        }

    } else {
        echo json_encode(["success" => false, "message" => "Utilisateur introuvable"]);
    }
}

//////////////////////////////////////////////////////
// VERIFY 2FA
//////////////////////////////////////////////////////
function verify2FA($data)
{
    global $conn;

    $email = $data["email"];
    $code = $data["code"];

    $query = "SELECT otp_code, otp_expiration FROM Clients WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        echo json_encode(["success" => false, "message" => "Utilisateur introuvable"]);
        return;
    }

    if ($user["otp_code"] == $code && strtotime($user["otp_expiration"]) > time()) {
        echo json_encode(["success" => true, "message" => "Connexion validée"]);
    } else {
        echo json_encode(["success" => false, "message" => "Code invalide ou expiré"]);
    }
}

//////////////////////////////////////////////////////
// LOGIN LIVREUR
//////////////////////////////////////////////////////
function loginLivreur($data)
{
    global $conn;

    $email = $data["email"];
    $password = $data["password"];

    $query = "SELECT * FROM Livreur WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($livreur = mysqli_fetch_assoc($result)) {

        if ($livreur["mdp"] === $password) {

            mysqli_query($conn, "
                UPDATE Livreur
                SET disponibilite=1
                WHERE idLivreur='".$livreur["idLivreur"]."'
            ");

            echo json_encode([
                "success" => true,
                "role" => "livreur",
                "livreur" => $livreur
            ]);

        } else {
            echo json_encode(["success" => false, "message" => "Mot de passe incorrect"]);
        }

    } else {
        echo json_encode(["success" => false, "message" => "Livreur introuvable"]);
    }
}

//////////////////////////////////////////////////////
// LOGOUT LIVREUR
//////////////////////////////////////////////////////
function logoutLivreur($data)
{
    global $conn;

    $id = $data["idLivreur"];

    mysqli_query($conn, "
        UPDATE Livreur
        SET disponibilite=0
        WHERE idLivreur='$id'
    ");

    echo json_encode(["success" => true]);
}

//////////////////////////////////////////////////////
// ROUTER
//////////////////////////////////////////////////////
switch ($request_method) {

    case 'GET':
        getUsers();
        break;

    case 'POST':

        if (isset($data["action"])) {

            switch ($data["action"]) {

                case "login":
                    loginUser($data);
                    break;

                case "verify2FA":
                    verify2FA($data);
                    break;

                case "loginLivreur":
                    loginLivreur($data);
                    break;

                case "registerLivreur":
                    addLivreur($data);
                    break;

                case "logoutLivreur":
                    logoutLivreur($data);
                    break;

                default:
                    addUser($data);
            }

        } else {
            addUser($data);
        }

        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}
?>