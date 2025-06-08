<?php
// Session-Konfiguration für lokale Entwicklung und Produktion

// Basis-Pfad ermitteln
$isLocal = (
    $_SERVER['HTTP_HOST'] === 'localhost' || 
    $_SERVER['HTTP_HOST'] === '127.0.0.1' ||
    strpos($_SERVER['HTTP_HOST'], 'localhost:') === 0
);

// Prüfe ob Subdomain verwendet wird
$isSubdomain = strpos($_SERVER['HTTP_HOST'], 'mimamori.') === 0;

if ($isSubdomain) {
    $sessionPath = '/'; // Root-Pfad für Subdomain
} else {
    $sessionPath = $isLocal ? '/mimamori/' : '/';
}

ini_set('session.cookie_path', $sessionPath);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);

// Session starten, falls noch nicht gestartet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>