// app.js - Funcionalidad común de TechShop

document.addEventListener("DOMContentLoaded", () => {
    console.log("TechShop cargado");
    
    // Manejo del botón de pago
    const botonPagar = document.getElementById("pagar");
    if (botonPagar) {
        botonPagar.addEventListener("click", () => {
            alert("Funcionalidad de pago en desarrollo");
        });
    }
});
