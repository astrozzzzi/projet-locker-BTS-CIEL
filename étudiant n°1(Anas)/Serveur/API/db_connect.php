<?php

$host = "172.18.199.9"; 
$user = "anas";
$password = "jesuisgentil42";
$dbname = "test";

// 🔗 connexion
$conn = mysqli_connect($host, $user, $password, $dbname);

// ❌ si erreur
if (!$conn) {
    die(json_encode([
        "success" => false,
        "message" => "Erreur connexion BDD: " . mysqli_connect_error()
    ]));
}

?>