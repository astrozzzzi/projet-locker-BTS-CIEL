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
// GET USERS
//////////////////////////////////////////////////////
function getUsers()
{
    global $conn;

    $query = "SELECT idClients, nom, prenom, email, telephone FROM Clients";
    $result = mysqli_query($conn, $query);

    $users = [];

    while($row = mysqli_fetch_assoc($result))
    {
        $users[] = $row;
    }

    echo json_encode($users);
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

    if(mysqli_num_rows($result) > 0)
    {
        echo json_encode(["success"=>false,"message"=>"Email déjà utilisé"]);
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

    if(mysqli_query($conn, $query))
        echo json_encode(["success"=>true,"message"=>"Compte créé"]);
    else
        echo json_encode(["success"=>false,"message"=>mysqli_error($conn)]);
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

    if(mysqli_num_rows($result) > 0)
    {
        echo json_encode(["success"=>false,"message"=>"Email déjà utilisé"]);
        return;
    }

    $idQuery = "SELECT MAX(idLivreur) as maxId FROM Livreur";
    $idResult = mysqli_query($conn, $idQuery);
    $row = mysqli_fetch_assoc($idResult);

    $newId = ($row["maxId"] ?? 0) + 1;

    $query = "
        INSERT INTO Livreur (idLivreur, nom, prenom, telephone, mdp, email, disponibilite)
        VALUES ('$newId','$nom','$prenom','$telephone','$password','$email','indisponible')
    ";

    if(mysqli_query($conn, $query))
        echo json_encode(["success"=>true,"message"=>"Compte livreur créé"]);
    else
        echo json_encode(["success"=>false,"message"=>mysqli_error($conn)]);
}

//////////////////////////////////////////////////////
// LOGIN CLIENT
//////////////////////////////////////////////////////
function loginUser($data)
{
    global $conn;

    $email = $data["email"];
    $password = $data["password"];

    $query = "SELECT * FROM Clients WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if($user = mysqli_fetch_assoc($result))
    {
        if($user["mdp"] === $password)
        {
            echo json_encode([
                "success"=>true,
                "role"=>"client",
                "user"=>$user
            ]);
        }
        else
        {
            echo json_encode(["success"=>false,"message"=>"Mot de passe incorrect"]);
        }
    }
    else
    {
        echo json_encode(["success"=>false,"message"=>"Utilisateur introuvable"]);
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

    if($livreur = mysqli_fetch_assoc($result))
    {
        if($livreur["mdp"] === $password)
        {
            echo json_encode([
                "success"=>true,
                "role"=>"livreur",
                "livreur"=>$livreur
            ]);
        }
        else
        {
            echo json_encode(["success"=>false,"message"=>"Mot de passe incorrect"]);
        }
    }
    else
    {
        echo json_encode(["success"=>false,"message"=>"Livreur introuvable"]);
    }
}

//////////////////////////////////////////////////////
// GENERATE 2FA (EMAIL + 5 MIN)
//////////////////////////////////////////////////////
function generate2FA($data)
{
    global $conn;

    $email = $data["email"];

    $code = rand(100000, 999999);
    $expiration = date("Y-m-d H:i:s", time() + 300); // 5 minutes

    $query = "
        UPDATE Clients 
        SET otp_code='$code', otp_expiration='$expiration'
        WHERE email='$email'
    ";

    mysqli_query($conn, $query);

    $sent = sendOTPEmail($email, $code);

    echo json_encode([
        "success" => true,
        "message" => $sent ? "Code envoyé par email" : "Erreur envoi email",
        "debug_code" => $code
    ]);
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

    if(!$user)
    {
        echo json_encode(["success"=>false,"message"=>"Utilisateur introuvable"]);
        return;
    }

    if($user["otp_code"] == $code && strtotime($user["otp_expiration"]) > time())
    {
        echo json_encode(["success"=>true,"message"=>"Connexion validée"]);
    }
    else
    {
        echo json_encode(["success"=>false,"message"=>"Code invalide ou expiré"]);
    }
}

//////////////////////////////////////////////////////
// ROUTER
//////////////////////////////////////////////////////
switch($request_method)
{
    case 'GET':
        getUsers();
        break;

    case 'POST':

        if(isset($data["action"]))
        {
            if($data["action"] === "login")
                loginUser($data);

            else if($data["action"] === "loginLivreur")
                loginLivreur($data);

            else if($data["action"] === "registerLivreur")
                addLivreur($data);

            else if($data["action"] === "generate2FA")
                generate2FA($data);

            else if($data["action"] === "verify2FA")
                verify2FA($data);

            else
                addUser($data);
        }
        else
        {
            addUser($data);
        }

        break;

    default:
        http_response_code(405);
        echo json_encode(["message"=>"Méthode non autorisée"]);
        break;
}