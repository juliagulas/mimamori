// register.js
document
  .getElementById("registerForm")
  .addEventListener("submit", async (e) => {
    e.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    try {
      const response = await fetch("api/register.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({ email, password }),
      });

      const contentType = response.headers.get("content-type");

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

      if (result.status === "success") {
        alert("Registration successful! You can now log in.");
        window.location.href = "/login.html";
      } else {
        alert(result.message || "Registration failed.");
      }
    } catch (error) {
      console.error("Error:", error);
      alert(`Fehler bei der Registrierung: ${error.message}`);
    }
  });

