const token = localStorage.getItem("token");

async function apuntarse() {
    const res = await fetch("../../backend/api/v1/membresias/apuntarse.php", {
        method: "POST",
        headers: { "Authorization": token }
    });
    const data = await res.json();
    alert(data.message || data.error);
}

async function renovar() {
    const res = await fetch("../../backend/api/v1/membresias/renovar.php", {
        method: "POST",
        headers: { "Authorization": token }
    });
    const data = await res.json();
    alert(data.message || data.error);
}

async function cancelarMembresia() {
    if (!confirm("¿Estás seguro de que deseas darte de baja?")) return;

    try {
        const response = await fetch("../../backend/api/v1/membresias/cancelar_membresias.php", {
            method: "DELETE",
            headers: { "Authorization": token }
        });

        const data = await response.json();
        alert(data.message || data.error);
        actualizarEstadoMembresia(); // recarga estado
    } catch (err) {
        console.error("Error al cancelar membresía:", err);
        alert("No se pudo cancelar la membresía");
    }
}

async function actualizarEstadoMembresia() {
    if (!token) {
        console.log("No hay token");
        return;
    }

    try {
        const response = await fetch("../../backend/api/v1/membresias/estado.php", {
            headers: {
                "Authorization": token
            }
        });
        const data = await response.json();

        if (data.error) {
            console.error(data.error);
            return;
        }

        const contenedor = document.getElementById("estado-membresia");
        contenedor.innerHTML = ""; // Limpiar

        if (data.tiene_membresia) {
            contenedor.innerHTML = `
                <p>Tienes membresía activa hasta: ${new Date(data.fecha_fin).toLocaleDateString()}</p>
                <div class="botones-membresia">
                    <button id="btn-renovar">Renovar - ${data.precio_renovacion}€</button>
                    <button id="btn-baja">Darse de baja</button>
                </div>
            `;

            document.getElementById("btn-renovar").onclick = () => {
                window.location.href = `pago.html?accion=renovar&precio=${data.precio_renovacion}`;
            };

            document.getElementById("btn-baja").onclick = cancelarMembresia;

        } else {
            contenedor.innerHTML = `
                <p>No tienes membresía activa</p>
                <div class="botones-membresia">
                    <button id="btn-apuntarse">Apuntarse - ${data.precio_membresia}€</button>
                </div>
            `;

            document.getElementById("btn-apuntarse").onclick = () => {
                window.location.href = `pago.html?accion=apuntarse&precio=${data.precio_membresia}`;
            };
        }

    } catch (error) {
        console.error("Error al obtener estado de membresía:", error);
    }
}

document.addEventListener("DOMContentLoaded", actualizarEstadoMembresia);

