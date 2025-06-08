<?php
// api/mimamori.php
// Vereinfachter API-Endpunkt für Mimamori-bezogene Anfragen

require_once '../system/session_config.php';
header('Content-Type: application/json');

// Prüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        "status" => "error",
        "message" => "Nicht eingeloggt"
    ]);
    exit;
}

require_once '../system/config_selector.php';
require_once '../system/mimamori_functions.php';

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

// Setze den letzten Aktualisierungszeitpunkt, wenn er noch nicht existiert
if (!isset($_SESSION['last_happiness_update'])) {
    $_SESSION['last_happiness_update'] = time();
}

switch ($action) {
    case 'get':
        // Mimamori-Daten des Benutzers abrufen
        $mimamori = getUserMimamori($userId);
        
        if (!$mimamori) {
            http_response_code(404);
            echo json_encode([
                "status" => "error",
                "message" => "Benutzer nicht gefunden"
            ]);
            exit;
        }
        
        // Füge verfügbare Futtertypen hinzu
        $foodItems = getFoodItems();
        
        echo json_encode([
            "status" => "success",
            "mimamori" => $mimamori,
            "foodItems" => $foodItems
        ]);
        break;
        
    case 'update_happiness':
        // Session-basierte Aktualisierung der Zufriedenheit
        $updatedMimamori = updateHappinessInSession($userId);
        
        if ($updatedMimamori) {
            echo json_encode([
                "status" => "success",
                "mimamori" => $updatedMimamori
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Fehler bei der Aktualisierung der Zufriedenheit"
            ]);
        }
        break;
        
    case 'feed':
        // Mimamori füttern
        $foodId = $_POST['food_id'] ?? 0;
        
        if (!$foodId) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Ungültiger Parameter: food_id"
            ]);
            exit;
        }
        
        // Futterwert ermitteln
        $foodItems = getFoodItems();
        $foodValue = null;
        
        foreach ($foodItems as $food) {
            if ($food['id'] == $foodId) {
                $foodValue = $food['happiness_value'];
                break;
            }
        }
        
        if ($foodValue === null) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Ungültiges Futter"
            ]);
            exit;
        }
        
        $success = feedMimamori($userId, $foodValue);
        
        if ($success) {
            // Aktualisierte Daten zurückgeben
            $updatedMimamori = getUserMimamori($userId);
            
            echo json_encode([
                "status" => "success",
                "message" => "Mimamori erfolgreich gefüttert",
                "mimamori" => $updatedMimamori
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Fehler beim Füttern des Mimamori"
            ]);
        }
        break;
        
    case 'types':
        // Alle verfügbaren Mimamori-Typen abrufen
        $types = getMimamoriTypes();
        
        echo json_encode([
            "status" => "success",
            "types" => $types
        ]);
        break;
        
    case 'change_type':
        // Mimamori-Typ ändern
        $typeId = $_POST['type_id'] ?? 0;
        
        if (!$typeId) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Ungültiger Parameter: type_id"
            ]);
            exit;
        }
        
        $success = changeMimamoriType($userId, $typeId);
        
        if ($success) {
            // Aktualisierte Daten zurückgeben
            $updatedMimamori = getUserMimamori($userId);
            
            echo json_encode([
                "status" => "success",
                "message" => "Mimamori-Typ erfolgreich geändert",
                "mimamori" => $updatedMimamori
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Fehler beim Ändern des Mimamori-Typs"
            ]);
        }
        break;
        
    default:
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Ungültige Aktion"
        ]);
}