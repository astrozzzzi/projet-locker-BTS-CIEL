<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include("db_connect.php");

$request_method = $_SERVER["REQUEST_METHOD"];
$data = json_decode(file_get_contents("php://input"), true);

/* ===================== GET USERS ===================== */
function getUsers()
{
    global $conn;

    $query = "SELECT id, nom, prenom, email, telephone FROM users";
    $result = mysqli_query($conn, $query);

    $users = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }

    echo json_encode($users, JSON_PRETTY_PRINT);
}

/* ===================== REGISTER ===================== */
function addUser($data)
{
    global $conn;

    $nom = $data["nom"];
    $prenom = $data["prenom"];
    $email = $data["email"];
    $telephone = $data["telephone"];
    $password = $data["password"];

    // ⚠️ Version simple (à sécuriser après)
    $query = "INSERT INTO users (nom, prenom, email, telephone, password) 
              VALUES ('$nom','$prenom','$email','$telephone','$password')";

    if (mysqli_query($conn, $query)) {
        echo json_encode(["success" => true, "message" => "Compte créé"]);
    } else {
        echo json_encode(["success" => false, "message" => mysqli_error($conn)]);
    }
}

/* ===================== LOGIN ===================== */
function loginUser($data)
{
    global $conn;

    $email = $data["email"];
    $password = $data["password"];

    $query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($user = mysqli_fetch_assoc($result)) {

        if ($user["password"] === $password) {
            echo json_encode([
                "success" => true,
                "message" => "Connexion réussie",
                "user" => [
                    "id" => $user["id"],
                    "nom" => $user["nom"],
                    "prenom" => $user["prenom"],
                    "email" => $user["email"]
                ]
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Mot de passe incorrect"
            ]);
        }

    } else {
        echo json_encode([
            "success" => false,
            "message" => "Utilisateur non trouvé"
        ]);
    }
}

/* ===================== ROUTER ===================== */
switch($request_method)
{
    case 'GET':
        getUsers();
        break;

    case 'POST':
        if (isset($data["action"]) && $data["action"] === "login") {
            loginUser($data);
        } else {
            addUser($data);
        }
        break;

    default:
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}
?>