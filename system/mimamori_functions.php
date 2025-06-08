<?php
// mimamori_functions.php
// Vereinfachte Funktionen für die Verwaltung der Mimamori-Charaktere

require_once 'config.php';

/**
 * Ruft die Mimamori-Daten eines Benutzers ab und aktualisiert die Zufriedenheit basierend auf der vergangenen Zeit
 * 
 * @param int $userId ID des Benutzers
 * @return array|null Mimamori-Daten oder null, wenn nicht gefunden
 */
function getUserMimamori($userId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT u.id, u.mimamori_type_id, u.happiness_level, u.last_update, 
                   t.name as type_name, t.color_primary, t.color_secondary
            FROM users u
            JOIN mimamori_types t ON u.mimamori_type_id = t.id
            WHERE u.id = :userId
        ");
        
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Zufriedenheit basierend auf vergangener Zeit aktualisieren
            calculateAndUpdateHappiness($user['id'], $user['last_update']);
            
            // Aktualisierte Daten abrufen
            $stmt = $pdo->prepare("
                SELECT u.id, u.mimamori_type_id, u.happiness_level, u.last_update, 
                       t.name as type_name, t.color_primary, t.color_secondary
                FROM users u
                JOIN mimamori_types t ON u.mimamori_type_id = t.id
                WHERE u.id = :userId
            ");
            
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return null;
    } catch (PDOException $e) {
        error_log("Fehler beim Abrufen des Mimamori: " . $e->getMessage());
        return null;
    }
}

/**
 * Berechnet und aktualisiert die Zufriedenheit basierend auf der vergangenen Zeit seit dem letzten Update
 * 
 * @param int $userId ID des Benutzers
 * @param string $lastUpdate Zeitstempel der letzten Aktualisierung
 * @return bool Erfolg der Aktualisierung
 */
function calculateAndUpdateHappiness($userId, $lastUpdate) {
    global $pdo;
    
    try {
        // Zeitdifferenz in Stunden berechnen
        $lastUpdateTime = strtotime($lastUpdate);
        $currentTime = time();
        $hoursDiff = floor(($currentTime - $lastUpdateTime) / 3600);
        
        if ($hoursDiff > 0) {
            // Aktuelle Zufriedenheit abrufen
            $stmt = $pdo->prepare("SELECT happiness_level FROM users WHERE id = :userId");
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $currentLevel = $stmt->fetchColumn();
            
            // Neue Zufriedenheit berechnen (Abnahme von 1 Level pro Stunde, aber nie unter 1)
            $newLevel = max(1, $currentLevel - min($hoursDiff, $currentLevel - 1));
            
            // Aktualisieren
            $stmt = $pdo->prepare("
                UPDATE users 
                SET happiness_level = :newLevel, last_update = NOW() 
                WHERE id = :userId
            ");
            
            $stmt->bindParam(':newLevel', $newLevel, PDO::PARAM_INT);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            return $stmt->execute();
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Fehler bei der Aktualisierung der Zufriedenheit: " . $e->getMessage());
        return false;
    }
}

/**
 * Aktualisiert die Zufriedenheit basierend auf der aktuellen Session, wenn eine bestimmte Zeit vergangen ist
 * Diese Funktion wird periodisch aufgerufen, während der Benutzer aktiv ist
 * 
 * @param int $userId ID des Benutzers
 * @return array|bool Aktualisierte Mimamori-Daten oder false bei Fehler
 */
function updateHappinessInSession($userId) {
    global $pdo;
    
    try {
        // Zeitpunkt des letzten Updates aus der Session abrufen oder jetzt setzen
        $lastSessionUpdate = $_SESSION['last_happiness_update'] ?? time();
        $currentTime = time();
        
        // Berechnen, ob eine Stunde vergangen ist (3600 Sekunden)
        $timeDiff = $currentTime - $lastSessionUpdate;
        
        // Wenn weniger als eine Stunde vergangen ist, nichts tun
        if ($timeDiff < 3600) {
            return getUserMimamori($userId);
        }
        
        // Berechnen, wie viele volle Stunden vergangen sind
        $hoursDiff = floor($timeDiff / 3600);
        
        // Aktuelle Zufriedenheit abrufen
        $stmt = $pdo->prepare("SELECT happiness_level FROM users WHERE id = :userId");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $currentLevel = $stmt->fetchColumn();
        
        // Neue Zufriedenheit berechnen (Abnahme von 1 Level pro Stunde, aber nie unter 1)
        $newLevel = max(1, $currentLevel - min($hoursDiff, $currentLevel - 1));
        
        // Aktualisieren
        $stmt = $pdo->prepare("
            UPDATE users 
            SET happiness_level = :newLevel, last_update = NOW() 
            WHERE id = :userId
        ");
        
        $stmt->bindParam(':newLevel', $newLevel, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $success = $stmt->execute();
        
        if ($success) {
            // Session-Zeitstempel aktualisieren
            $_SESSION['last_happiness_update'] = $currentTime;
            
            // Aktualisierte Daten zurückgeben
            return getUserMimamori($userId);
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Fehler bei der Session-basierten Aktualisierung: " . $e->getMessage());
        return false;
    }
}

/**
 * Erhöht die Zufriedenheit des Benutzers durch Füttern
 * 
 * @param int $userId ID des Benutzers
 * @param int $foodValue Zufriedenheitswert des Futters
 * @return bool Erfolg der Fütterung
 */
function feedMimamori($userId, $foodValue) {
    global $pdo;
    
    try {
        // Aktuelle Zufriedenheit abrufen
        $stmt = $pdo->prepare("SELECT happiness_level FROM users WHERE id = :userId");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $currentLevel = $stmt->fetchColumn();
        
        // Neue Zufriedenheit berechnen (maximal 10)
        $newLevel = min(10, $currentLevel + $foodValue);
        
        // Zufriedenheit aktualisieren
        $stmt = $pdo->prepare("
            UPDATE users 
            SET happiness_level = :newLevel, last_update = NOW()
            WHERE id = :userId
        ");
        
        $stmt->bindParam(':newLevel', $newLevel, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        
        // Session-Zeitstempel aktualisieren
        $_SESSION['last_happiness_update'] = time();
        
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Fehler beim Füttern des Mimamori: " . $e->getMessage());
        return false;
    }
}

/**
 * Ruft alle verfügbaren Mimamori-Typen ab
 * 
 * @return array Liste der Mimamori-Typen
 */
function getMimamoriTypes() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT * FROM mimamori_types");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Fehler beim Abrufen der Mimamori-Typen: " . $e->getMessage());
        return [];
    }
}

/**
 * Ändert den Mimamori-Typ eines Benutzers
 * 
 * @param int $userId ID des Benutzers
 * @param int $typeId ID des neuen Typs
 * @return bool Erfolg der Änderung
 */
function changeMimamoriType($userId, $typeId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET mimamori_type_id = :typeId
            WHERE id = :userId
        ");
        
        $stmt->bindParam(':typeId', $typeId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Fehler beim Ändern des Mimamori-Typs: " . $e->getMessage());
        return false;
    }
}

/**
 * Gibt eine Liste der verfügbaren Nahrungsmittel zurück
 * 
 * @return array Liste der Nahrungsmittel
 */
function getFoodItems() {
    // Da wir keine separate Nahrungstabelle mehr haben, geben wir die Werte direkt zurück
    return [
        ['id' => 1, 'name' => 'Apfel', 'happiness_value' => 1, 'image_class' => 'apple'],
        ['id' => 2, 'name' => 'Banane', 'happiness_value' => 2, 'image_class' => 'banana'],
        ['id' => 3, 'name' => 'Keks', 'happiness_value' => 3, 'image_class' => 'cookie']
    ];
}