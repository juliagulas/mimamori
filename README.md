# Mimamori - Virtuelles Haustier Web-App

## 🎮 Über Mimamori

Mimamori ist eine liebevolle Web-Anwendung, die ein virtuelles Haustier simuliert, das Pflege und Aufmerksamkeit benötigt. Benutzer können ihr Mimamori füttern, um dessen Zufriedenheit zu erhöhen, und müssen regelmäßig zurückkehren, da die Zufriedenheit mit der Zeit abnimmt.

### Hauptfunktionen

- **Virtuelles Haustier**: Wähle zwischen 4 verschiedenen Mimamori-Typen (Rosa, Blau, Grün, Lila)
- **Fütterungssystem**: Füttere dein Mimamori mit verschiedenen Nahrungsmitteln (Apfel, Banane, Keks)
- **Zufriedenheitsmechanik**: Die Zufriedenheit sinkt um 1 Punkt pro Stunde
- **Visuelle Rückmeldung**: Das Mimamori ändert seinen Gesichtsausdruck basierend auf der Zufriedenheit
- **Responsive Design**: Funktioniert auf Desktop und Mobilgeräten

## 🕐 Zeitliche Mechanik

Die Zufriedenheit des Mimamori funktioniert wie folgt:
- **Startwert**: 5 von 10 Punkten
- **Abnahme**: -1 Punkt pro Stunde (automatisch berechnet)
- **Minimum**: Die Zufriedenheit kann nicht unter 1 fallen
- **Maximum**: Die Zufriedenheit kann nicht über 10 steigen

### Fütterungswerte:
- 🍎 **Apfel**: +1 Zufriedenheit
- 🍌 **Banane**: +2 Zufriedenheit
- 🍪 **Keks**: +3 Zufriedenheit

## 🚀 Installation auf Infomaniak

### Voraussetzungen

- Infomaniak Hosting-Account
- MySQL-Datenbank
- PHP 7.4 oder höher
- FTP-Zugang oder File Manager

### Schritt-für-Schritt Anleitung

#### 1. Datenbank vorbereiten

1. Logge dich ins Infomaniak Control Panel ein
2. Gehe zu **Hosting → Datenbanken**
3. Erstelle eine neue Datenbank oder nutze eine bestehende
4. Notiere dir:
   - Datenbankname (z.B. `zc8fb7_mimamori`)
   - Benutzername
   - Passwort
   - **Host/Server** (wichtig: oft NICHT `localhost`!)

#### 2. Dateien hochladen

Lade folgende Dateien/Ordner via FTP oder File Manager hoch:

```
/api/              → Alle API-Endpunkte
/css/              → Stylesheets
/js/               → JavaScript-Dateien
/system/           → PHP-Konfiguration
index.html         → Startseite
login.html         → Login-Seite
register.html      → Registrierung
mimamori-feeding-ui.html → Hauptanwendung
protected.html     → Geschützter Bereich
```

**Nicht hochladen:**
- Debug-Dateien (debug_*.php)
- Test-Dateien (test_*.php)
- Lokale Konfiguration (config_local.php)

#### 3. Konfiguration anpassen

1. Benenne `system/config_production.php.example` in `system/config_production.php` um (falls vorhanden)
2. Öffne `system/config_production.php` und trage ein:

```php
$host = 'zc8fb7.myd.infomaniak.com';  // Dein MySQL-Host
$db   = 'zc8fb7_mimamori';           // Dein Datenbankname
$user = 'zc8fb7_mimamori';           // Dein Benutzername
$pass = 'DEIN_PASSWORT';             // Dein Passwort
```

#### 4. Datenbank einrichten

1. Öffne phpMyAdmin über das Infomaniak Control Panel
2. Wähle deine Datenbank aus
3. Klicke auf **Importieren**
4. Importiere nacheinander:
   - `system/db_schema.sql` (Tabellenstruktur)
   - `system/db_initial_data.sql` (Mimamori-Typen)

#### 5. Sicherheit

Erstelle eine `.htaccess` Datei im `/system/` Ordner:

```apache
Deny from all
```

#### 6. Testen

1. Öffne deine Domain (z.B. `https://mimamori.example.ch`)
2. Registriere einen Test-Account
3. Logge dich ein und teste die Funktionen

## 🛠️ Lokale Entwicklung

### Voraussetzungen

- XAMPP oder ähnlicher lokaler Webserver
- PHP 7.4+
- MySQL/MariaDB

### Installation

1. Klone das Repository in `htdocs/mimamori`
2. Erstelle eine Datenbank namens `mimamori_local`
3. Importiere die SQL-Dateien aus `/system/`
4. Die App erkennt automatisch die lokale Umgebung

### Zugriff

```
http://localhost/mimamori/
```

## 📁 Projektstruktur

```
mimamori/
├── api/                 # API-Endpunkte
│   ├── login.php       # Login-Verarbeitung
│   ├── register.php    # Registrierung
│   ├── mimamori.php    # Mimamori-Aktionen
│   └── protected.php   # Session-Check
├── css/                # Styles
│   └── style.css      # Haupt-Stylesheet
├── js/                 # JavaScript
│   ├── js-config.js   # Pfad-Konfiguration
│   ├── login.js       # Login-Logik
│   └── ...           # Weitere Scripts
├── system/            # Backend-Konfiguration
│   ├── config_selector.php      # Auto-Config
│   ├── config_production.php    # Server-Config
│   ├── mimamori_functions.php   # Kern-Funktionen
│   └── db_*.sql                # Datenbank-Dateien
└── *.html             # Frontend-Seiten
```

## 🔧 Fehlerbehebung

### "Database connection failed"
- Prüfe die Zugangsdaten in `config_production.php`
- Stelle sicher, dass der Host korrekt ist (nicht immer `localhost`!)

### Session-Probleme
- Lösche Browser-Cookies
- Prüfe ob die Domain korrekt ist

### 404 Fehler
- Stelle sicher, dass alle Dateien hochgeladen wurden
- Beachte Groß-/Kleinschreibung auf Linux-Servern

## 📝 Lizenz

Dieses Projekt ist für Bildungszwecke erstellt. Alle Rechte vorbehalten.

## 👩‍💻 Entwickelt von

Entwickelt mit ❤️ für Julia