<?php

$host = "172.18.199.9";
$user = "anas";
$password = "jesuisgentil42";
$dbname = "bddLocker";
$port = 3307;

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die(json_encode([
        "success" => false,
        "message" => "Erreur connexion DB : " . mysqli_connect_error()
    ]));
}

?>