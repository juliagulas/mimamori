// login.js - Version mit Debug-Ausgaben
document
  .getElementById("loginForm")
  .addEventListener("submit", async (e) => {
    e.preventDefault();
    console.log("Login form submitted");

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    try {
      console.log("Sending login request...");
      const response = await fetch("api/login.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({ email, password }),
        credentials: "include"  // Wichtig für Session-Cookies
      });

      const contentType = response.headers.get("content-type");
      console.log("Response status:", response.status);

      if (!response.ok) {
        let errorMessage = `Server returned status ${response.status}`;
        if (contentType && contentType.includes("application/json")) {
          const errorData = await response.json();
          errorMessage = errorData.message || errorMessage;
        } else {
          const text = await response.text();
          errorMessage = text || errorMessage;
        }
        throw new Error(errorMessage);
      }

      const result = await response.json();
      console.log("Login result:", result);

      if (result.status === "success") {
        console.log("Login successful! Redirecting to:", "/mimamori/mimamori-feeding-ui.html");
        alert("Login erfolgreich!");
        
        // Kurze Verzögerung, damit der Alert sichtbar ist
        setTimeout(() => {
          window.location.href = "/mimamori-feeding-ui.html";
        }, 100);
      } else {
        alert(result.message || "Login fehlgeschlagen.");
      }
    } catch (error) {
      console.error("Login error:", error);
      alert(`Fehler beim Login: ${error.message}`);
    }
  });