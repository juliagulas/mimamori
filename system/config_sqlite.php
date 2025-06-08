<?php
// config_sqlite.php
// SQLite Konfiguration für lokale Entwicklung

$dbPath = __DIR__ . '/mimamori.db';

try {
    $dsn = "sqlite:" . $dbPath;
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // SQLite-spezifische Einstellungen
    $pdo->exec("PRAGMA foreign_keys = ON");
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $e->getMessage()
    ]);
    exit;
}
?>