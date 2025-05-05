document.addEventListener("DOMContentLoaded", () => {
    console.log("Módulo de productos cargado");

    const productosLista = document.getElementById("productos-lista");

    // Función para cargar productos desde la API
    async function cargarProductos() {
        try {
            const response = await fetch("../../backend/api/v1/products/index.php"); // Ajusta la ruta si es necesario
            const productos = await response.json();
            console.log(productos); // Verifica que los productos se carguen correctamente

            productosLista.innerHTML = "";
            productos.forEach(producto => {
                const item = document.createElement("div");
                item.classList.add("producto-item");
                item.innerHTML = `
                    <h3>${producto.name || "Nombre del Producto"}</h3>
                    <p>Precio: $${producto.price || "0.00"}</p>
                    <p>Descripción: ${producto.description || "Descripción no disponible"}</p>
                    <img src="${producto.image_url}" alt="${producto.name}" style="max-width: 200px; display: block; margin: 10px 0;">
                    <button class="btn-add-cart" data-id="${producto.id}" data-nombre="${producto.name}" data-precio="${producto.price}" data-imagen="${producto.image_url}">Añadir al carrito</button>
                `;
                productosLista.appendChild(item);
            });
        } catch (error) {
            console.error("Error al cargar productos", error);
            productosLista.innerHTML = "<p>Error al cargar los productos.</p>";
        }
    }

    // Cargar productos al cargar la página
    cargarProductos();

    // Función para agregar al carrito
    function agregarAlCarrito(id, nombre, precio, imagen) {
        console.log("Producto añadido al carrito:", nombre, precio);
        let carrito = JSON.parse(localStorage.getItem("carrito")) || [];

        // Verifica si el producto ya está en el carrito, si es así, aumenta la cantidad
        const productoExistente = carrito.find(item => item.id === id);

        if (productoExistente) {
            productoExistente.cantidad += 1;  // Si el producto ya existe, incrementa la cantidad
        } else {
            // Si no existe, lo agrega con cantidad 1
            carrito.push({ id, nombre, precio, imagen, cantidad: 1 });
        }

        // Guardar el carrito actualizado en localStorage
        localStorage.setItem("carrito", JSON.stringify(carrito));
        alert("Producto añadido al carrito.");
    }

    // Añadir evento de clic a los botones de añadir al carrito
    document.addEventListener("click", (event) => {
        if (event.target && event.target.classList.contains("btn-add-cart")) {
            const idProducto = event.target.getAttribute("data-id");
            const nombreProducto = event.target.getAttribute("data-nombre");
            const precioProducto = event.target.getAttribute("data-precio");
            const imagenProducto = event.target.getAttribute("data-imagen");

            // Verificar si los valores obtenidos son válidos
            if (idProducto && nombreProducto && precioProducto && imagenProducto) {
                // Llamar a la función para agregar el producto al carrito
                agregarAlCarrito(idProducto, nombreProducto, precioProducto, imagenProducto);
            } else {
                console.error("Error: No se han encontrado todos los datos del producto.");
            }
        }
    });
});

