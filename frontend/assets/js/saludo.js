document.addEventListener("DOMContentLoaded", () => {
    const saludoUsuario = document.getElementById("saludo-usuario");
    const btnLogin = document.getElementById("btn-login");
    const btnLogout = document.getElementById("btn-logout");

    const usuario = JSON.parse(localStorage.getItem("usuario"));

    if (usuario && usuario.nombre) {
        saludoUsuario.textContent = `Hola, ${usuario.nombre}`;
        btnLogin.style.display = "none";
        btnLogout.style.display = "inline";
    }

    btnLogout.addEventListener("click", (e) => {
        e.preventDefault();
        localStorage.removeItem("usuario");
        location.reload(); // recarga para actualizar la interfaz
    });
});
