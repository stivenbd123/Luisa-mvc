<?php

require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';


require_once __DIR__ . '/../PHPMailer/src/SMTP.php';


require_once __DIR__ . '/../PHPMailer/src/Exception.php';


use PHPMailer\PHPMailer\PHPMailer;


function enviarCorreo($destino, $nombre, $asunto, $mensaje) {

    
    $mail = new PHPMailer(true);

    
    try {

        $mail->isSMTP();

        $mail->Host = 'smtp.gmail.com';

        $mail->SMTPAuth = true;

        $mail->Username = 'lluigy2407@gmail.com';

        $mail->Password = 'fdod gins sqzc spei';

        $mail->SMTPSecure = 'tls';

        $mail->Port = 587;

        $mail->setFrom('lluigy2407@gmail.com', 'fdod gins sqzc spei');

        $mail->addAddress($destino, $nombre);

        $mail->isHTML(false);

        $mail->Subject = $asunto;

        $mail->Body = $mensaje;

        $mail->send();

    } catch (Exception $e) {

        echo "Error al enviar: {$mail->ErrorInfo}";

    }
    // Fin del try/catch de envío.

}

?>