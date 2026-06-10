<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';

//////////////////////////////////////////////////////
// CONFIG EMAIL SENDER
//////////////////////////////////////////////////////

function sendOTPEmail($email, $code)
{
    $mail = new PHPMailer(true);

    try {

        // ================= SMTP CONFIG =================
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // ⚠️ Mets TON email Gmail ici
        $mail->Username = 'yourgmail@gmail.com';

        // ⚠️ MOT DE PASSE D'APPLICATION GOOGLE (pas ton vrai mdp)
        $mail->Password = 'your_app_password';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // ================= SENDER =================
        $mail->setFrom('yourgmail@gmail.com', 'Mon Application');
        $mail->addAddress($email);

        // ================= CONTENT =================
        $mail->isHTML(true);
        $mail->Subject = "Votre code de vérification (2FA)";

        $mail->Body = "
            <div style='font-family: Arial; padding: 10px'>
                <h2 style='color:#007bff'>Authentification sécurisée</h2>
                <p>Voici votre code de vérification :</p>

                <div style='font-size:28px; font-weight:bold; letter-spacing:5px; color:#000'>
                    $code
                </div>

                <p style='margin-top:20px'>
                    Ce code est valable <b>5 minutes</b>.
                </p>

                <hr>
                <small>Si vous n'êtes pas à l'origine de cette action, ignorez cet email.</small>
            </div>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}

?>