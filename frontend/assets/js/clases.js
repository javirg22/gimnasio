document.addEventListener("DOMContentLoaded", () => {
    console.log("Módulo de clases cargado");

    function obtenerUserIdDelToken() {
        const token = localStorage.getItem("token");
        if (!token) return null;
        try {
            const payloadBase64 = token.split('.')[1];
            const payloadJson = atob(payloadBase64);
            const payload = JSON.parse(payloadJson);
            return payload.userId || payload.id || null;
        } catch {
            return null;
        }
    }

    const userId = obtenerUserIdDelToken();
    console.log("userId extraído del token:", userId);
    if (!userId) {
        console.error("Usuario no autenticado, no puede reservar clases.");
        return;
    }

    const clasesLista = document.getElementById("clases-lista");

    async function cargarClases() {
        try {
            const response = await fetch("../../backend/api/v1/clases/index.php");
            const clases = await response.json();
            console.log(clases);

            clasesLista.innerHTML = "";
            clases.forEach(clase => {
                const item = document.createElement("div");
                item.classList.add("clase-item");
                item.innerHTML = `
                    <h3>${clase.nombre || "Nombre de la clase"}</h3>
                    <p>Fecha y hora: ${clase.fecha_hora || "Sin horario"}</p>
                    <p>Aforo disponible: ${clase.aforo || 0}</p>
                    <p>Aforo total: ${clase.aforo_disponible || 0}</p>
                    <button class="btn-reservar" data-id="${clase.id_clase}">Reservar</button>
                `;
                clasesLista.appendChild(item);
            });
        } catch (error) {
            console.error("Error al cargar clases", error);
            clasesLista.innerHTML = "<p>Error al cargar las clases.</p>";
        }
    }

    async function reservarClase(id_clase) {
        try {
            console.log("Intentando reservar clase con:", { id_usuario: userId, id_clase: id_clase });
            const token = localStorage.getItem("token");
            if (!token) {
                alert("Debe iniciar sesión para reservar.");
                return;
            }

            const bodyData = { id_usuario: userId, id_clase: id_clase };
            console.log("JSON enviado:", JSON.stringify(bodyData));

            const response = await fetch("../../backend/api/v1/clases/reservar.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${token}`
                },
                body: JSON.stringify(bodyData)
            });

            const result = await response.json();

            if (response.ok) {
                alert("Clase reservada con éxito.");
            } else {
                alert(`Error al reservar clase: ${result.error || "Desconocido"}`);
            }
        } catch (error) {
            console.error("Error en la reserva", error);
            alert("Error al reservar la clase.");
        }
    }

    document.addEventListener("click", (event) => {
        if (event.target && event.target.classList.contains("btn-reservar")) {
            const claseId = event.target.getAttribute("data-id");
            if (claseId) {
                reservarClase(claseId);
            }
        }
    });

    cargarClases();
});

