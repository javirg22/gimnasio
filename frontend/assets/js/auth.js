document.addEventListener("DOMContentLoaded", () => {
    console.log("Módulo de autenticación cargado");
    
    const registroForm = document.getElementById("registro-form");
    const loginForm = document.getElementById("login-form");
    const logoutBtn = document.getElementById("logout-btn"); // Por si tienes un botón de logout

    // Si ya hay una sesión iniciada, redirigimos fuera de login o registro
    const tokenExistente = localStorage.getItem("token");
    const paginaActual = window.location.pathname;

    if (tokenExistente) {
        // Evitar acceso a login o registro si ya hay sesión
        if (paginaActual.includes("login.html") || paginaActual.includes("registro.html")) {
            alert("Ya hay una sesión iniciada. Por favor, cierra sesión primero.");
            window.location.href = "index.html"; // o donde quieras mandarlo
        }
    }

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

    // Función para cerrar sesión
    function logout() {
        localStorage.removeItem("token");
        alert("Sesión cerrada");
        window.location.href = "login.html"; // O donde quieras mandar al usuario al cerrar sesión
    }

    // Función para obtener el token actual
    function getToken() {
        return localStorage.getItem("token");
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

    // Si tienes un botón de logout en alguna página, lo conectas así
    if (logoutBtn) {
        logoutBtn.addEventListener("click", () => {
            logout();
        });
    }
});
