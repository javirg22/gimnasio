document.addEventListener("DOMContentLoaded", () => {
    const carritoContenedor = document.getElementById("carrito-contenedor");
    const totalElement = document.getElementById("total-carrito");

    // Función para cargar los productos del carrito
    const cargarCarrito = () => {
        const carrito = JSON.parse(localStorage.getItem('carrito')) || [];

        // Limpiar el contenedor
        carritoContenedor.innerHTML = '';

        let total = 0;

        carrito.forEach(producto => {
            const item = document.createElement("div");
            item.classList.add("producto-carrito");
            item.innerHTML = `
                <img src="${producto.imagen}" alt="${producto.nombre}">
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

    // Función para eliminar un producto del carrito o reducir su cantidad
    window.eliminarProductoDelCarrito = (button) => {
        const id = button.getAttribute("data-id"); // Obtener el id del producto desde el botón
        let carrito = JSON.parse(localStorage.getItem('carrito')) || [];

        // Encontramos el producto en el carrito
        const productoEncontrado = carrito.find(producto => producto.id === id);

        if (productoEncontrado) {
            // Si el producto tiene más de 1 en cantidad, solo reducimos la cantidad
            if (productoEncontrado.cantidad > 1) {
                productoEncontrado.cantidad--;
                console.log(`Cantidad reducida para ${productoEncontrado.nombre}. Nueva cantidad: ${productoEncontrado.cantidad}`);
            } else {
                // Si la cantidad es 1, eliminamos el producto del carrito
                carrito = carrito.filter(producto => producto.id !== id);
                console.log(`Producto ${productoEncontrado.nombre} eliminado completamente.`);
            }
        } else {
            console.log("Producto no encontrado en el carrito.");
        }

        // Guardamos el carrito actualizado en localStorage
        localStorage.setItem('carrito', JSON.stringify(carrito));

        // Actualizamos la vista del carrito
        cargarCarrito();
    };

    // Delegado de eventos para manejar el click en los botones de eliminar
    carritoContenedor.addEventListener("click", (event) => {
        if (event.target && event.target.classList.contains("eliminar")) {
            eliminarProductoDelCarrito(event.target); // Pasamos el botón que fue clickeado
        }
    });

    // Función para vaciar el carrito
    window.vaciarCarrito = () => {
        localStorage.removeItem('carrito');
        cargarCarrito(); // Actualizar el carrito después de vaciar
    };

    // Función para realizar la compra
    window.realizarCompra = () => {
        if (localStorage.getItem('carrito')) {
            window.location.href = "pago.html"; // Redirige a la página de pago
        } else {
            alert("El carrito está vacío.");
        }
    };

    // Cargar el carrito al cargar la página
    cargarCarrito();
});
