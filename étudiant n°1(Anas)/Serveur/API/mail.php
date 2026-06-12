<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';

// ─────────────────────────────────────────────
// Fonction générique d'envoi
// ─────────────────────────────────────────────
function sendMail($to, $subject, $htmlBody)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'yourgmail@gmail.com';   // ⚠️ à remplacer
        $mail->Password   = 'your_app_password';      // ⚠️ mot de passe d'application Google
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('yourgmail@gmail.com', 'Locker');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}

// ─────────────────────────────────────────────
// Email code 2FA (inscription / connexion)
// ─────────────────────────────────────────────
function sendOTPEmail($email, $code)
{
    $html = "
        <div style='font-family:Arial;padding:20px'>
            <h2 style='color:#007bff'>Vérification de votre compte</h2>
            <p>Voici votre code de vérification :</p>
            <div style='font-size:32px;font-weight:bold;letter-spacing:8px;
                        color:#1a1a2e;background:#f4f6f9;padding:16px;
                        border-radius:8px;display:inline-block;margin:10px 0'>
                $code
            </div>
            <p>Ce code est valable <strong>5 minutes</strong>.</p>
            <hr>
            <small style='color:#888'>Si vous n'êtes pas à l'origine de cette action, ignorez cet email.</small>
        </div>
    ";

    return sendMail($email, "Votre code de vérification – Locker", $html);
}

// ─────────────────────────────────────────────
// Email quand un livreur est attribué au colis
// ─────────────────────────────────────────────
function envoyerEmailAttribution($conn, $idClient, $numColis)
{
    // Récupère l'email du client
    $result = mysqli_query($conn,
        "SELECT email, prenom FROM Clients WHERE idClients='$idClient' LIMIT 1"
    );

    $client = mysqli_fetch_assoc($result);

    if (!$client) return false;

    $prenom = $client["prenom"];
    $email  = $client["email"];

    $html = "
        <div style='font-family:Arial;padding:20px'>
            <h2 style='color:#007bff'>Votre colis a un livreur !</h2>
            <p>Bonjour <strong>$prenom</strong>,</p>
            <p>
                Votre colis numéro <strong style='color:#1a1a2e'>$numColis</strong>
                vient d'être attribué à un livreur.
            </p>
            <p>Il sera bientôt déposé dans le locker que vous avez choisi.</p>
            <div style='margin:20px 0;padding:14px 20px;background:#f4f6f9;
                        border-left:4px solid #007bff;border-radius:4px'>
                <strong>N° de colis :</strong> $numColis<br>
                <strong>Statut :</strong> Attribué à un livreur
            </div>
            <p>Vous serez notifié à nouveau quand le colis sera disponible dans le locker.</p>
            <hr>
            <small style='color:#888'>Locker – Cité scolaire Mauriac-Desgranges</small>
        </div>
    ";

    return sendMail($email, "Votre colis $numColis a un livreur – Locker", $html);
}

?>