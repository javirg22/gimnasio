<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Punto de entrada del sistema
require __DIR__ . '/autoload.php';

// Verificar si Composer está instalado correctamente
if (!file_exists(__DIR__ . '/autoload.php')) {
    die('Error: Composer no está instalado. Ejecuta "composer install".');
}

// Inicialización del sistema
echo "Vendor cargado correctamente.";
