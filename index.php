<?php
/**
 * API REST Sakila - Arquitectura MVC
 * Punto de entrada principal de la aplicación
 */

// Configurar headers CORS y manejo de errores
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/config/Database.php';

// Configurar headers
Config::setCorsHeaders();

// Manejar OPTIONS request para CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configurar manejo de errores
set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function($exception) {
    Config::sendResponse(500, null, "Error interno del servidor: " . $exception->getMessage());
});

try {
    // Cargar el sistema de rutas
    require_once __DIR__ . '/routes/api.php';
    
    // Procesar la petición
    Router::dispatch();
    
} catch (Exception $e) {
    Config::sendResponse(500, null, "Error interno del servidor: " . $e->getMessage());
}
?>