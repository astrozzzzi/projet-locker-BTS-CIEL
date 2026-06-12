<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

include("db_connect.php");
require_once("mail.php");

$data = json_decode(file_get_contents("php://input"), true);
$method = $_SERVER["REQUEST_METHOD"];


// ================= GET USERS =================
function getUsers()
{
    global $conn;

    $clients = [];
    $livreurs = [];

    $res1 = mysqli_query($conn,
        "SELECT idClients, nom, prenom, email, telephone
         FROM Clients"
    );

    while($row = mysqli_fetch_assoc($res1)){
        $row["type"] = "client";
        $clients[] = $row;
    }

    $res2 = mysqli_query($conn,
        "SELECT idLivreur, nom, prenom, email, telephone, disponibilite
         FROM Livreur"
    );

    while($row = mysqli_fetch_assoc($res2)){
        $row["type"] = "livreur";
        $livreurs[] = $row;
    }

    echo json_encode([
        "clients" => $clients,
        "livreurs" => $livreurs
    ]);
}


// ================= REGISTER CLIENT =================
function addUser($data)
{
    global $conn;

    $nom = $data["nom"];
    $prenom = $data["prenom"];
    $email = $data["email"];
    $telephone = $data["telephone"];
    $password = $data["password"];

    $check = mysqli_query(
        $conn,
        "SELECT idClients FROM Clients WHERE email='$email'"
    );

    if(mysqli_num_rows($check) > 0){
        echo json_encode([
            "success"=>false,
            "message"=>"Email déjà utilisé"
        ]);
        return;
    }

    $query = "
        INSERT INTO Clients
        (nom, prenom, email, telephone, mdp)
        VALUES
        ('$nom','$prenom','$email','$telephone','$password')
    ";

    if(mysqli_query($conn,$query)){
        echo json_encode([
            "success"=>true,
            "message"=>"Compte créé"
        ]);
    }else{
        echo json_encode([
            "success"=>false,
            "message"=>mysqli_error($conn)
        ]);
    }
}


// ================= REGISTER LIVREUR =================
function addLivreur($data)
{
    global $conn;

    $nom = $data["nom"];
    $prenom = $data["prenom"];
    $email = $data["email"];
    $telephone = $data["telephone"];
    $password = $data["password"];

    $check = mysqli_query(
        $conn,
        "SELECT idLivreur FROM Livreur WHERE email='$email'"
    );

    if(mysqli_num_rows($check) > 0){
        echo json_encode([
            "success"=>false,
            "message"=>"Email déjà utilisé"
        ]);
        return;
    }

    $query = "
        INSERT INTO Livreur
        (nom, prenom, email, telephone, mdp, disponibilite)
        VALUES
        ('$nom','$prenom','$email','$telephone','$password','0')
    ";

    if(mysqli_query($conn,$query)){
        echo json_encode([
            "success"=>true,
            "message"=>"Livreur créé"
        ]);
    }else{
        echo json_encode([
            "success"=>false,
            "message"=>mysqli_error($conn)
        ]);
    }
}


// ================= LOGIN CLIENT =================
function loginUser($data)
{
    global $conn;

    $email = $data["email"];
    $password = $data["password"];

    $q = mysqli_query(
        $conn,
        "SELECT * FROM Clients WHERE email='$email' LIMIT 1"
    );

    if($u = mysqli_fetch_assoc($q)){

        if($u["mdp"] == $password){

            echo json_encode([
                "success"=>true,
                "type"=>"client",
                "user"=>$u
            ]);

        }else{

            echo json_encode([
                "success"=>false,
                "message"=>"Mot de passe incorrect"
            ]);
        }

    }else{

        echo json_encode([
            "success"=>false,
            "message"=>"Utilisateur introuvable"
        ]);
    }
}


// ================= LOGIN LIVREUR =================
function loginLivreur($data)
{
    global $conn;

    $email = $data["email"];
    $password = $data["password"];

    $q = mysqli_query(
        $conn,
        "SELECT * FROM Livreur WHERE email='$email' LIMIT 1"
    );

    if($u = mysqli_fetch_assoc($q)){

        if($u["mdp"] == $password){

            mysqli_query($conn,"
                UPDATE Livreur
                SET disponibilite=1
                WHERE idLivreur='".$u["idLivreur"]."'
            ");

            echo json_encode([
                "success"=>true,
                "type"=>"livreur",
                "user"=>$u
            ]);

        }else{

            echo json_encode([
                "success"=>false,
                "message"=>"Mot de passe incorrect"
            ]);
        }

    }else{

        echo json_encode([
            "success"=>false,
            "message"=>"Livreur introuvable"
        ]);
    }
}


// ================= LOGOUT LIVREUR =================
function logoutLivreur($data)
{
    global $conn;

    $idLivreur = $data["idLivreur"];

    mysqli_query($conn,"
        UPDATE Livreur
        SET disponibilite=0
        WHERE idLivreur='$idLivreur'
    ");

    echo json_encode([
        "success"=>true
    ]);
}


// ================= GENERATE 2FA =================
function generate2FA($data)
{
    global $conn;

    $email = $data["email"];
    $code = rand(100000,999999);

    mysqli_query($conn,"
        UPDATE Clients
        SET otp_code='$code'
        WHERE email='$email'
    ");

    $sent = sendOTPEmail($email,$code);

    echo json_encode([
        "success"=>true,
        "mail"=>$sent,
        "message"=>"Code envoyé"
    ]);
}


// ================= VERIFY 2FA =================
function verify2FA($data)
{
    global $conn;

    $email = $data["email"];
    $code = $data["code"];

    $q = mysqli_query(
        $conn,
        "SELECT otp_code FROM Clients WHERE email='$email'"
    );

    $u = mysqli_fetch_assoc($q);

    if($u && $u["otp_code"] == $code){

        echo json_encode([
            "success"=>true,
            "message"=>"Code valide"
        ]);

    }else{

        echo json_encode([
            "success"=>false,
            "message"=>"Code invalide"
        ]);
    }
}


// ================= UPDATE DISPONIBILITE =================
function updateDisponibilite($data)
{
    global $conn;

    $idLivreur = $data["idLivreur"];
    $disponibilite = $data["disponibilite"];

    $q = mysqli_query($conn,"
        UPDATE Livreur
        SET disponibilite='$disponibilite'
        WHERE idLivreur='$idLivreur'
    ");

    if($q){

        echo json_encode([
            "success"=>true,
            "message"=>"Disponibilité mise à jour"
        ]);

    }else{

        echo json_encode([
            "success"=>false,
            "message"=>mysqli_error($conn)
        ]);
    }
}


// ================= ROUTER =================
if($method === "GET"){
    getUsers();
    exit;
}

if(isset($data["action"])){

    switch($data["action"]){

        case "login":
            loginUser($data);
            break;

        case "loginLivreur":
            loginLivreur($data);
            break;

        case "registerLivreur":
            addLivreur($data);
            break;

        case "generate2FA":
            generate2FA($data);
            break;

        case "verify2FA":
            verify2FA($data);
            break;

        case "updateDisponibilite":
            updateDisponibilite($data);
            break;

        case "logoutLivreur":
            logoutLivreur($data);
            break;

        default:
            addUser($data);
            break;
    }

}else{
    addUser($data);
}

?>