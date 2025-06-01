document.addEventListener("DOMContentLoaded", () => {
    const resumenPedido = document.getElementById("resumen-pedido");
    const totalPagoElemento = document.getElementById("total-pago");
    const formEnvio = document.getElementById("form-envio");
    const paypalContainer = document.getElementById("paypal-container");

    // Función para obtener userId desde token JWT almacenado
    function obtenerUserIdDelToken() {
        const token = localStorage.getItem("token");
        if (!token) return null;
        try {
            const payload = JSON.parse(atob(token.split('.')[1]));
            return payload.userId || payload.id || null;
        } catch {
            return null;
        }
    }

    // Obtener userId
    const userId = obtenerUserIdDelToken();

    // Cargar carrito específico del usuario
    const carrito = userId ? JSON.parse(localStorage.getItem(`carrito_${userId}`)) || [] : [];

    // Obtener parámetros URL (membresía)
    function obtenerParametrosURL() {
        const params = new URLSearchParams(window.location.search);
        return {
            accion: params.get("accion"),  // para membresía: "nueva" o "renovar"
            precio: parseFloat(params.get("precio")) // precio de membresía
        };
    }
    const { accion, precio } = obtenerParametrosURL();

    let precioMembresia = (!isNaN(precio) && accion) ? precio : 0;
    let descuentoAplicado = false;
    let descuento = 0;

    // Calcular total productos del carrito
    let totalCarrito = carrito.reduce((acc, prod) => acc + (parseFloat(prod.precio) * prod.cantidad), 0);

    // Mostrar resumen y código descuento (solo si no aplicado)
    function mostrarResumen() {
        resumenPedido.innerHTML = "";

        if (carrito.length > 0) {
            carrito.forEach(producto => {
                const precioPorProducto = (parseFloat(producto.precio) * producto.cantidad).toFixed(2);
                const item = document.createElement("p");
                item.textContent = `${producto.nombre} - $${producto.precio} x ${producto.cantidad} = $${precioPorProducto}`;
                resumenPedido.appendChild(item);
            });
        }

        if (precioMembresia > 0) {
            const tipoMembresia = accion === "renovar" ? "Renovación de membresía" : "Nueva membresía";
            const membresiaTexto = document.createElement("p");
            membresiaTexto.textContent = `${tipoMembresia}: ${precioMembresia.toFixed(2)} €`;
            resumenPedido.appendChild(membresiaTexto);
        }

        if (descuentoAplicado) {
            const descuentoTexto = document.createElement("p");
            descuentoTexto.style.color = "green";
            descuentoTexto.textContent = `Descuento aplicado: -${descuento.toFixed(2)} €`;
            resumenPedido.appendChild(descuentoTexto);
        }

        // Solo mostrar input de descuento si no aplicado y hay algo que pagar
        if (!descuentoAplicado && (carrito.length > 0 || precioMembresia > 0)) {
            const label = document.createElement("label");
            label.setAttribute("for", "descuento");
            label.textContent = "Código de descuento:";
            resumenPedido.appendChild(label);

            const input = document.createElement("input");
            input.type = "text";
            input.id = "descuento";
            input.placeholder = "Introduce código";
            resumenPedido.appendChild(input);
        }
    }

    function actualizarTotal() {
        let totalBruto = totalCarrito + precioMembresia;
        let totalFinal = totalBruto - descuento;
        if (totalFinal < 0) totalFinal = 0;
        totalPagoElemento.textContent = `Total a pagar: ${totalFinal.toFixed(2)} €`;
        return totalFinal.toFixed(2);
    }

    mostrarResumen();
    actualizarTotal();

    formEnvio.addEventListener("submit", (e) => {
        e.preventDefault();

        if (!descuentoAplicado) {
            const codigoInput = document.getElementById("descuento");
            if (codigoInput) {
                const codigo = codigoInput.value.trim().toLowerCase();
                if (codigo === "aprobado") {
                    const totalBruto = totalCarrito + precioMembresia;
                    descuento = +(totalBruto * 0.10).toFixed(2);
                    descuentoAplicado = true;
                } else {
                    descuento = 0;
                    alert("Código de descuento inválido");
                }
            }
        }

        mostrarResumen();
        const totalFinal = actualizarTotal();

        paypalContainer.style.display = "block";

        // Solo crear botón PayPal si no existe
        if (!document.getElementById("paypal-button-container").hasChildNodes()) {
            paypal.Buttons({
                createOrder: function (data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: totalFinal
                            }
                        }]
                    });
                },
                onApprove: function (data, actions) {
                    return actions.order.capture().then(function (details) {
                        alert("Pago completado por " + details.payer.name.given_name);

                        // Función para actualizar membresía si hay membresía que pagar
                        function actualizarMembresia() {
                            return fetch('http://localhost:8001/backend/api/v1/membresias/update_membership.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Authorization': localStorage.getItem('token')
                                },
                                body: JSON.stringify({
                                    diasDuracion: 30
                                })
                            })
                            .then(res => res.json());
                        }

                        const promesas = [];

                        if (precioMembresia > 0) {
                            promesas.push(actualizarMembresia());
                        }

                        Promise.all(promesas).then(() => {
                            if (carrito.length > 0) {
                                localStorage.removeItem(`carrito_${userId}`);
                            }
                            window.location.href = "pago_exitoso.html";
                        });
                    });
                }
            }).render('#paypal-button-container');
        }
    });
});

