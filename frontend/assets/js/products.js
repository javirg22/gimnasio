document.addEventListener("DOMContentLoaded", () => {
    console.log("Módulo de productos cargado");

    // Función para extraer userId del token JWT almacenado en localStorage
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

    const productosLista = document.getElementById("productos-lista");

    // --- NUEVO: Referencia al input de búsqueda
    const inputBusqueda = document.getElementById("busqueda");

    // --- NUEVO: Guardar todos los productos para poder filtrar
    let productos = [];

    async function cargarProductos() {
        try {
            const response = await fetch("../../backend/api/v1/products/index.php");
            const data = await response.json();
            console.log("Respuesta recibida:", data);

            if (!Array.isArray(data)) {
                throw new Error(data.error || "La respuesta del servidor no es un array de productos.");
            }

            // Guardamos todos los productos para luego filtrar
            productos = data;

            mostrarProductos(productos);
        } catch (error) {
            console.error("Error al cargar productos", error);
            productosLista.innerHTML = "<p>Error al cargar los productos.</p>";
        }
    }

    // --- NUEVO: Función para mostrar productos (puede mostrar una lista filtrada)
    function mostrarProductos(lista) {
        productosLista.innerHTML = "";
        lista.forEach(producto => {
            const item = document.createElement("div");
            item.classList.add("producto-item");
            item.innerHTML = `
                <h3>${producto.name || "Nombre del Producto"}</h3>
                <p>Precio: $${producto.price || "0.00"}</p>
                <p>Descripción: ${producto.description || "Descripción no disponible"}</p>
                <img src="../${producto.image_url}" alt="${producto.name}" style="max-width: 200px; display: block; margin: 10px 0;">
                <button class="btn-add-cart" data-id="${producto.id}" data-nombre="${producto.name}" data-precio="${producto.price}" data-imagen="${producto.image_url}">Añadir al carrito</button>
            `;
            productosLista.appendChild(item);
        });
    }

    // --- NUEVO: Evento input para filtrar productos
    if (inputBusqueda) {
        inputBusqueda.addEventListener("input", () => {
            const texto = inputBusqueda.value.toLowerCase();
            const filtrados = productos.filter(p => 
                (p.name && p.name.toLowerCase().includes(texto)) || 
                (p.description && p.description.toLowerCase().includes(texto))
            );
            mostrarProductos(filtrados);
        });
    } else {
        console.warn("No se encontró el input de búsqueda con id 'busqueda'");
    }

    function agregarAlCarrito(id, nombre, precio, imagen) {
        console.log("Producto añadido al carrito:", nombre, precio);
        let carrito = JSON.parse(localStorage.getItem(keyCarrito)) || [];

        const productoExistente = carrito.find(item => item.id === id);

        if (productoExistente) {
            productoExistente.cantidad += 1;
        } else {
            carrito.push({ id, nombre, precio, imagen, cantidad: 1 });
        }

        localStorage.setItem(keyCarrito, JSON.stringify(carrito));
        alert("Producto añadido al carrito.");
    }

    document.addEventListener("click", (event) => {
        if (event.target && event.target.classList.contains("btn-add-cart")) {
            if (event.target.disabled) return;

            const idProducto = event.target.getAttribute("data-id");
            const nombreProducto = event.target.getAttribute("data-nombre");
            const precioProducto = parseFloat(event.target.getAttribute("data-precio"));
            const imagenProducto = event.target.getAttribute("data-imagen");

            if (idProducto && nombreProducto && !isNaN(precioProducto) && imagenProducto) {
                agregarAlCarrito(idProducto, nombreProducto, precioProducto, imagenProducto);
            } else {
                console.error("Error: No se han encontrado todos los datos del producto.");
            }
        }
    });

    cargarProductos();
});

