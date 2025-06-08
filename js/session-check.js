// Session-Check für geschützte Seiten
async function checkSession() {
    console.log("Checking session...");
    
    try {
        const response = await fetch("/mimamori/api/protected.php", {
            method: "GET",
            credentials: "include",
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        console.log("Session check response status:", response.status);

        if (!response.ok || response.status === 401) {
            console.log("Not authenticated, redirecting to login...");
            window.location.href = "/mimamori/login.html";
            return false;
        }

        const result = await response.json();
        console.log("Session check result:", result);
        
        if (result.status !== "success") {
            console.log("Session invalid, redirecting to login...");
            window.location.href = "/mimamori/login.html";
            return false;
        }

        // Session ist gültig
        console.log("Session valid for user:", result.email);
        return true;
        
    } catch (error) {
        console.error("Session check failed:", error);
        // Bei Fehler nicht sofort weiterleiten - könnte ein Netzwerkproblem sein
        console.warn("Session check error - allowing access but functionality may be limited");
        return true; // Erlaubt erstmal Zugriff
    }
}

// Session beim Laden der Seite überprüfen, aber warte bis DOM geladen ist
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', async function() {
        // Kleine Verzögerung um sicherzustellen, dass alle Scripts geladen sind
        setTimeout(async () => {
            await checkSession();
        }, 100);
    });
} else {
    // DOM ist schon geladen
    setTimeout(async () => {
        await checkSession();
    }, 100);
}