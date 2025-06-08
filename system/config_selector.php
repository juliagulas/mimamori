<?php
// config_selector.php - Wählt automatisch die richtige Konfiguration

// Erkennt ob wir lokal (localhost) oder auf dem Server sind
$isLocal = (
    $_SERVER['HTTP_HOST'] === 'localhost' || 
    $_SERVER['HTTP_HOST'] === '127.0.0.1' ||
    strpos($_SERVER['HTTP_HOST'], 'localhost:') === 0
);

if ($isLocal) {
    // Lokale Entwicklung
    require_once __DIR__ . '/config_local.php';
} else {
    // Produktionsserver (Infomaniak)
    require_once __DIR__ . '/config_production.php';
}

// Gemeinsame Konfiguration
define('IS_LOCAL', $isLocal);
define('BASE_PATH', $isLocal ? '/mimamori' : '');
define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . BASE_PATH);
?>