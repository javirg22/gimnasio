document.addEventListener("DOMContentLoaded", () => {
    // Mismo método para obtener userId
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
    if (!userId) {
        console.error("Usuario no autenticado, no se puede manejar carrito individual.");
        return;
    }

    const keyCarrito = `carrito_${userId}`;

    const carritoContenedor = document.getElementById("carrito-contenedor");
    const totalElement = document.getElementById("total-carrito");

    const cargarCarrito = () => {
        const carrito = JSON.parse(localStorage.getItem(keyCarrito)) || [];

        carritoContenedor.innerHTML = '';
        let total = 0;

        carrito.forEach(producto => {
            const item = document.createElement("div");
            item.classList.add("producto-carrito");
            item.innerHTML = `
                <img src="../${producto.imagen}" alt="${producto.nombre}" style="width: 100px;">
                <h3>${producto.nombre}</h3>
                <p>Precio: $${producto.precio}</p>
                <p>Cantidad: ${producto.cantidad}</p>
                <p>Total por producto: $${(producto.precio * producto.cantidad).toFixed(2)}</p>
                <button class="eliminar" data-id="${producto.id}">Eliminar</button>
            `;
            carritoContenedor.appendChild(item);
            total += producto.precio * producto.cantidad;
        });

        totalElement.textContent = `Total: $${total.toFixed(2)}`;
    };

    window.eliminarProductoDelCarrito = (button) => {
        const id = button.getAttribute("data-id");
        let carrito = JSON.parse(localStorage.getItem(keyCarrito)) || [];

        const productoEncontrado = carrito.find(producto => producto.id === id);

        if (productoEncontrado) {
            if (productoEncontrado.cantidad > 1) {
                productoEncontrado.cantidad--;
                console.log(`Cantidad reducida para ${productoEncontrado.nombre}. Nueva cantidad: ${productoEncontrado.cantidad}`);
            } else {
                carrito = carrito.filter(producto => producto.id !== id);
                console.log(`Producto ${productoEncontrado.nombre} eliminado completamente.`);
            }
        } else {
            console.log("Producto no encontrado en el carrito.");
        }

        localStorage.setItem(keyCarrito, JSON.stringify(carrito));
        cargarCarrito();
    };

    carritoContenedor.addEventListener("click", (event) => {
        if (event.target && event.target.classList.contains("eliminar")) {
            eliminarProductoDelCarrito(event.target);
        }
    });

    window.vaciarCarrito = () => {
        localStorage.removeItem(keyCarrito);
        cargarCarrito();
    };

    window.realizarCompra = () => {
        if (localStorage.getItem(keyCarrito)) {
            window.location.href = "pago.html";
        } else {
            alert("El carrito está vacío.");
        }
    };

    cargarCarrito();
});
