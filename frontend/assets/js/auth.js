document.addEventListener("DOMContentLoaded", () => {
    console.log("Módulo de autenticación cargado");
    
    const registroForm = document.getElementById("registro-form");
    const loginForm = document.getElementById("login-form");

    // Función para registrar un usuario
    async function registrarUsuario(nombre, email, password) {
        try {
            const response = await fetch("../../backend/api/v1/auth/register.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ nombre, email, password })
            });
            const data = await response.json();
            
            if (data.token) {
                localStorage.setItem("token", data.token);
                alert("Registro exitoso. Redirigiendo...");
                window.location.href = "index.html";
            } else {
                alert(data.error || "Error en el registro");
            }
        } catch (error) {
            console.error("Error en el registro", error);
        }
    }

    // Función para iniciar sesión
    async function iniciarSesion(email, password) {
        try {
            const response = await fetch("../../backend/api/v1/auth/login.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ email, password })
            });
            const data = await response.json();
            
            if (data.token) {
                localStorage.setItem("token", data.token);
                alert("Inicio de sesión exitoso. Redirigiendo...");
                window.location.href = "index.html";
            } else {
                alert(data.error || "Error en el inicio de sesión");
            }
        } catch (error) {
            console.error("Error en el inicio de sesión", error);
        }
    }

    // Manejo del formulario de registro
    if (registroForm) {
        registroForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const nombre = document.getElementById("nombre").value;
            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;
            registrarUsuario(nombre, email, password);
        });
    }

    // Manejo del formulario de login
    if (loginForm) {
        loginForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const email = document.getElementById("login-email").value;
            const password = document.getElementById("login-password").value;
            iniciarSesion(email, password);
        });
    }
});
