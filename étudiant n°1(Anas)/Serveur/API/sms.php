<?php

// ============================================================
//  sms.php – Envoi de SMS via l'Arduino SIM808
//  Compatible avec bddLocker et le serveur Arduino
// ============================================================

define('ARDUINO_URL', 'http://192.168.1.100/sms'); // ⚠️ Adapter avec l'IP de votre Arduino

/**
 * Envoie un SMS à un client à partir de son idClients
 *
 * @param mysqli $conn     Connexion BDD (fournie par db_connect.php)
 * @param int    $idClient L'idClients du destinataire
 * @param string $message  Le texte du SMS (max 160 caractères)
 * @return bool            true si l'Arduino a répondu 200 OK
 */
function envoyerSMSClient($conn, int $idClient, string $message): bool
{
    // 1. Récupérer le numéro de téléphone du client dans la BDD
    $result = mysqli_query($conn,
        "SELECT telephone FROM clients WHERE idClients = '$idClient' LIMIT 1"
    );

    if (!$result || mysqli_num_rows($result) === 0) {
        error_log("[SMS] Client introuvable : idClients=$idClient");
        return false;
    }

    $row = mysqli_fetch_assoc($result);
    $telephone = trim($row['telephone']);

    if (empty($telephone)) {
        error_log("[SMS] Pas de téléphone pour idClients=$idClient");
        return false;
    }

    // 2. Tronquer à 160 caractères (limite d'un SMS GSM standard)
    $message = substr($message, 0, 160);

    // 3. Envoyer à l'Arduino au format JSON attendu : {"to":"...","message":"..."}
    $payload = json_encode([
        'to'      => $telephone,
        'message' => $message
    ]);

    $ch = curl_init(ARDUINO_URL);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,       // Arduino peut être lent à répondre
        CURLOPT_CONNECTTIMEOUT => 5,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        error_log("[SMS] Envoyé à $telephone (client $idClient)");
        return true;
    } else {
        error_log("[SMS] Échec envoi à $telephone – HTTP $httpCode – Réponse : $response");
        return false;
    }
}
