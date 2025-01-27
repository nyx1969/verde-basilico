<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Log degli errori in un file
ini_set('log_errors', 1);
ini_set('error_log', 'php-error.log');

// Headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Gestione della richiesta OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Verifica il metodo della richiesta
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

try {
    require 'vendor/autoload.php';

    // Log del corpo della richiesta
    $input = file_get_contents('php://input');
    error_log("Received input: " . $input);
    
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }

    // Log dei dati decodificati
    error_log("Decoded data: " . print_r($data, true));

    $name = $data['user_name'] ?? '';
    $email = $data['user_email'] ?? '';
    $phone = $data['user_phone'] ?? '';
    $menuType = $data['menu_type'] ?? '';
    $message = $data['message'] ?? '';

    if (empty($name) || empty($email) || empty($phone)) {
        throw new Exception('Missing required fields');
    }

    $mail = new PHPMailer(true);
    
    // Debug output
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->Debugoutput = function($str, $level) {
        error_log("SMTP Debug: $str");
    };

    // Configurazione SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'mlesca69@gmail.com';
    $mail->Password = 'zvih qhtf ccib lmvv';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom('mlesca69@gmail.com', 'Verde Basilico');
    $mail->addAddress('mlesca69@gmail.com');
    $mail->addReplyTo($email, $name);

    $mail->isHTML(true);
    $mail->Subject = "Nuovo ordine da $name";
    $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2>Nuovo ordine ricevuto</h2>
            <p><strong>Nome:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Telefono:</strong> $phone</p>
            <p><strong>Tipo Menu:</strong> $menuType</p>
            <p><strong>Messaggio:</strong><br>$message</p>
        </div>";

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Email inviata con successo']);

} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Errore durante l\'invio dell\'email',
        'error' => $e->getMessage()
    ]);
}
