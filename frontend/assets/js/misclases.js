const container = document.getElementById('clasesContainer');

function obtenerUserIdDesdeToken() {
    const rawToken = localStorage.getItem('token');
    if (!rawToken) {
        console.error("No hay token en localStorage");
        return null;
    }
    try {
        const base64 = rawToken.split('.')[1];
        const payload = JSON.parse(atob(base64));
        return payload.userId || null;
    } catch (error) {
        console.error("Error al parsear el token:", error);
        return null;
    }
}

const userId = obtenerUserIdDesdeToken();

if (!userId) {
    container.textContent = 'No se pudo obtener el ID del usuario.';
} else {
    fetch(`http://localhost:8001/backend/api/v1/clases/reservar.php?id_usuario=${userId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Error al obtener las clases');
        return response.json();
    })
    .then(data => {
        if (data.length === 0) {
            container.textContent = 'No tienes clases reservadas.';
            return;
        }
        console.log(data);

        container.innerHTML = '';
        data.forEach(clase => {
            const div = document.createElement('div');
            div.className = 'clase';

            div.innerHTML = `
                <h2>${clase.nombre}</h2>
                <p><strong>Fecha y hora:</strong> ${clase.fecha_hora}</p>
                <button class="btn-eliminar" data-id="${clase.id_clase}">Cancelar reserva</button>
            `;

            container.appendChild(div);
        });

        // Añadir evento a cada botón de eliminar
        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', () => {
                const claseId = btn.getAttribute('data-id');

                fetch('http://localhost:8001/backend/api/v1/clases/reservar.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_usuario: userId,
                        id_clase: parseInt(claseId)
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error('No se pudo eliminar la reserva');
                    return response.json();
                })
                .then(result => {
                    alert(result.message || "Reserva cancelada");
                    // Recargar la lista de clases reservadas
                    location.reload();
                })
                .catch(error => {
                    alert("Error al eliminar la reserva");
                    console.error(error);
                });
            });
        });
    })
    .catch(error => {
        container.textContent = 'Error al cargar las clases.';
        console.error(error);
    });
}

