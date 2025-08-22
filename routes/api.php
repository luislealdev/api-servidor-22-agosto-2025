<?php
require_once __DIR__ . '/Router.php';
require_once __DIR__ . '/../controllers/FilmController.php';
require_once __DIR__ . '/../controllers/ActorController.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

/**
 * Definición de rutas de la API
 */

// ============ RUTAS DE PELÍCULAS ============
// GET /films - Obtener todas las películas (con paginación y búsqueda)
Router::get('/films', function() {
    FilmController::index();
});

// GET /films/{id} - Obtener película por ID
Router::get('/films/{id}', function($id) {
    FilmController::show($id);
});

// POST /films - Crear nueva película
Router::post('/films', function() {
    FilmController::store();
});

// PUT /films/{id} - Actualizar película
Router::put('/films/{id}', function($id) {
    FilmController::update($id);
});

// DELETE /films/{id} - Eliminar película
Router::delete('/films/{id}', function($id) {
    FilmController::destroy($id);
});

// ============ RUTAS DE ACTORES ============
// GET /actors - Obtener todos los actores (con paginación y búsqueda)
Router::get('/actors', function() {
    ActorController::index();
});

// GET /actors/{id} - Obtener actor por ID
Router::get('/actors/{id}', function($id) {
    ActorController::show($id);
});

// GET /actors/{id}/films - Obtener películas de un actor
Router::get('/actors/{id}/films', function($id) {
    ActorController::getFilms($id);
});

// POST /actors - Crear nuevo actor
Router::post('/actors', function() {
    ActorController::store();
});

// PUT /actors/{id} - Actualizar actor
Router::put('/actors/{id}', function($id) {
    ActorController::update($id);
});

// DELETE /actors/{id} - Eliminar actor
Router::delete('/actors/{id}', function($id) {
    ActorController::destroy($id);
});

// ============ RUTAS DE CLIENTES ============
// GET /customers - Obtener todos los clientes (con paginación y búsqueda)
Router::get('/customers', function() {
    CustomerController::index();
});

// GET /customers/{id} - Obtener cliente por ID
Router::get('/customers/{id}', function($id) {
    CustomerController::show($id);
});

// GET /customers/{id}/rentals - Obtener historial de rentals de un cliente
Router::get('/customers/{id}/rentals', function($id) {
    CustomerController::getRentals($id);
});

// POST /customers - Crear nuevo cliente
Router::post('/customers', function() {
    CustomerController::store();
});

// PUT /customers/{id} - Actualizar cliente
Router::put('/customers/{id}', function($id) {
    CustomerController::update($id);
});

// DELETE /customers/{id} - Eliminar cliente
Router::delete('/customers/{id}', function($id) {
    CustomerController::destroy($id);
});

// ============ RUTA DE INFORMACIÓN DE LA API ============
Router::get('/', function() {
    $info = [
        'api_name' => 'Sakila API',
        'version' => '1.0.0',
        'description' => 'API REST para la base de datos Sakila',
        'endpoints' => [
            'films' => [
                'GET /films' => 'Obtener todas las películas',
                'GET /films/{id}' => 'Obtener película por ID',
                'POST /films' => 'Crear nueva película',
                'PUT /films/{id}' => 'Actualizar película',
                'DELETE /films/{id}' => 'Eliminar película'
            ],
            'actors' => [
                'GET /actors' => 'Obtener todos los actores',
                'GET /actors/{id}' => 'Obtener actor por ID',
                'GET /actors/{id}/films' => 'Obtener películas de un actor',
                'POST /actors' => 'Crear nuevo actor',
                'PUT /actors/{id}' => 'Actualizar actor',
                'DELETE /actors/{id}' => 'Eliminar actor'
            ],
            'customers' => [
                'GET /customers' => 'Obtener todos los clientes',
                'GET /customers/{id}' => 'Obtener cliente por ID',
                'GET /customers/{id}/rentals' => 'Obtener historial de rentals',
                'POST /customers' => 'Crear nuevo cliente',
                'PUT /customers/{id}' => 'Actualizar cliente',
                'DELETE /customers/{id}' => 'Eliminar cliente'
            ]
        ],
        'query_parameters' => [
            'page' => 'Número de página para paginación (default: 1)',
            'limit' => 'Elementos por página (default: 10)',
            'search' => 'Término de búsqueda'
        ]
    ];
    Config::sendResponse(200, $info, "Información de la API Sakila");
});
?>
