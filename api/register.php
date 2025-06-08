<?php
// register.php
require_once '../system/session_config.php';
header('Content-Type: application/json');

require_once '../system/config_selector.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        echo json_encode(["status" => "error", "message" => "Email and password sind erforderlich"]);
        exit;
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "Email wird bereits verwendet"]);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user with default Mimamori values
    $insert = $pdo->prepare("
        INSERT INTO users (email, password, mimamori_type_id, happiness_level, last_update) 
        VALUES (:email, :pass, 1, 5, NOW())
    ");
    
    try {
        $insert->execute([
            ':email' => $email,
            ':pass'  => $hashedPassword
        ]);
        echo json_encode(["status" => "success", "message" => "Registrierung erfolgreich!"]);
    } catch (PDOException $e) {
        // Log the error for debugging
        error_log("Registration error: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => "Datenbankfehler bei der Registrierung"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Ungültige Anfragemethode"]);
}