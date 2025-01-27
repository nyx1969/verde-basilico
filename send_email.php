<?php
header('Content-Type: application/json');

// Abilita la visualizzazione degli errori (solo per debug)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

try {
    // Recupera i dati dal form
    $data = json_decode(file_get_contents('php://input'), true);
    
    $name = $data['user_name'] ?? '';
    $email = $data['user_email'] ?? '';
    $phone = $data['user_phone'] ?? '';
    $menuType = $data['menu_type'] ?? '';
    $message = $data['message'] ?? '';

    // Validazione base
    if (empty($name) || empty($email) || empty($phone)) {
        throw new Exception('Tutti i campi sono obbligatori');
    }

    $mail = new PHPMailer(true);

    // Configurazione del server SMTP di Gmail
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'verdebasilico@gmail.com'; // Il tuo indirizzo Gmail
    $mail->Password = 'your-app-password';       // La password per le app generata da Google
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';

    // Impostazioni email
    $mail->setFrom('verdebasilico@gmail.com', 'Verde Basilico');
    $mail->addAddress('verdebasilico@gmail.com'); // Dove vuoi ricevere le email
    $mail->addReplyTo($email, $name);

    // Contenuto
    $mail->isHTML(true);
    $mail->Subject = "Nuovo ordine da $name";
    
    // Template HTML dell'email
    $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #4CAF50; border-bottom: 2px solid #4CAF50; padding-bottom: 10px;'>
                Nuovo ordine ricevuto
            </h2>
            
            <div style='background-color: #f9f9f9; padding: 20px; border-radius: 5px; margin: 20px 0;'>
                <p><strong style='color: #4CAF50;'>Nome:</strong> $name</p>
                <p><strong style='color: #4CAF50;'>Email:</strong> $email</p>
                <p><strong style='color: #4CAF50;'>Telefono:</strong> $phone</p>
                <p><strong style='color: #4CAF50;'>Tipo Menu:</strong> $menuType</p>
                <p><strong style='color: #4CAF50;'>Messaggio:</strong><br>
                " . nl2br(htmlspecialchars($message)) . "</p>
            </div>
            
            <div style='font-size: 12px; color: #666; margin-top: 20px; padding-top: 10px; border-top: 1px solid #eee;'>
                Questa email è stata inviata dal form di contatto di Verde Basilico
            </div>
        </div>
    ";

    // Versione testo semplice per client email che non supportano HTML
    $mail->AltBody = "
        Nuovo ordine ricevuto
        
        Nome: $name
        Email: $email
        Telefono: $phone
        Tipo Menu: $menuType
        Messaggio: $message
    ";

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Email inviata con successo']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Errore: ' . $e->getMessage()]);
}
