document.addEventListener("DOMContentLoaded", async () => {
  const token = localStorage.getItem("token");

  let saludo = "Hola invitado";

  if (token) {
    try {
      const payload = JSON.parse(atob(token.split('.')[1]));
      const userId = payload.userId;

      const response = await fetch(`http://localhost:8001/backend/api/v1/saludo/saludo.php?id=${userId}`);
      const data = await response.json();

      if (data && data.name) {
        saludo = `Hola ${data.name}`;
      }
    } catch (error) {
      console.error("Error al obtener el usuario:", error);
    }
  }

  const saludoElemento = document.getElementById("saludo");
  if (saludoElemento) {
    saludoElemento.textContent = saludo;
  }
});
