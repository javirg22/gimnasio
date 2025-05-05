document.addEventListener("DOMContentLoaded", () => {
    const resumenPedido = document.getElementById("resumen-pedido");
    const totalPagoElemento = document.getElementById("total-pago");
    const formEnvio = document.getElementById("form-envio");
    const formPagoContainer = document.getElementById("form-pago-container");
    const formPago = document.getElementById("form-pago");

    // Recuperar carrito del localStorage
    const carrito = JSON.parse(localStorage.getItem("carrito")) || [];

    function mostrarResumen() {
        resumenPedido.innerHTML = "";
        let total = 0;
    
        carrito.forEach(producto => {
            const item = document.createElement("p");
            
            // Mostrar nombre, cantidad y precio total por producto
            const precioPorProducto = (parseFloat(producto.precio) * producto.cantidad).toFixed(2); // Precio total por producto
            item.textContent = `${producto.nombre} - $${producto.precio} x ${producto.cantidad} = $${precioPorProducto}`;
            resumenPedido.appendChild(item);
    
            // Sumamos al total el precio por cantidad de cada producto
            total += parseFloat(precioPorProducto); // Usamos parseFloat() para convertir el precio a un número
        });
    
        // Mostrar el total
        totalPagoElemento.textContent = `Total a pagar: $${total.toFixed(2)}`; // .toFixed(2) para que el total tenga 2 decimales
    }
    
    mostrarResumen();

    // Mostrar formulario de pago después de llenar el de envío
    formEnvio.addEventListener("submit", (e) => {
        e.preventDefault();
        formEnvio.style.display = "none"; // Ocultar formulario de envío
        formPagoContainer.style.display = "block"; // Mostrar formulario de pago
    });

    // Al pagar, redirigir a la página de éxito
    formPago.addEventListener("submit", (e) => {
        e.preventDefault();
        localStorage.removeItem("carrito"); // Vaciar carrito tras el pago
        window.location.href = "pago_exitoso.html"; // Redirigir a la página de éxito
    });
});
