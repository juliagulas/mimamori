// js-config.js - Automatische Pfad-Erkennung für JavaScript

// Ermittle den Basis-Pfad basierend auf der URL
function getBasePath() {
    const hostname = window.location.hostname;
    const isLocal = hostname === 'localhost' || hostname === '127.0.0.1';
    
    // Wenn Subdomain 'mimamori' verwendet wird, kein Pfad nötig
    const isSubdomain = hostname.startsWith('mimamori.');
    
    if (isSubdomain) {
        return ''; // Kein Basis-Pfad bei Subdomain
    }
    
    return isLocal ? '/mimamori' : '';
}

// Globale Konstante für alle JavaScript-Dateien
const BASE_URL = getBasePath();

console.log('JavaScript Config loaded. BASE_URL:', BASE_URL);
console.log('Hostname:', window.location.hostname);