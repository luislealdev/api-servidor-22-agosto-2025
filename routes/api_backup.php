<?php
require_once __DIR__ . '/Router.php';
require_once __DIR__ . '/../controllers/FilmController.php';
require_once __DIR__ . '/../controllers/ActorController.php';
require_once __DIR__ . '/../controllers/CustomerController.php';
require_once __DIR__ . '/../controllers/WebController.php';

/**
 * Definición de rutas de la aplicación
 * - Rutas web (/) muestran vistas HTML
 * - Rutas API (/api/*) devuelven JSON
 */

// ============ RUTAS WEB (VISTAS HTML) ============
// GET / - Página principal del dashboard
Router::get('/', function() {
    WebController::home();
});

// GET /films - Vista de películas
Router::get('/films', function() {
    WebController::films();
});

// GET /actors - Vista de actores
Router::get('/actors', function() {
    WebController::actors();
});

// GET /customers - Vista de clientes
Router::get('/customers', function() {
    WebController::customers();
});

// ============ RUTAS API (JSON) ============
// GET /api/films - Obtener todas las películas (con paginación y búsqueda)
Router::get('/api/films', function() {
    FilmController::index();
});

// GET /api/films/{id} - Obtener película por ID
Router::get('/api/films/{id}', function($id) {
    FilmController::show($id);
});

// POST /api/films - Crear nueva película
Router::post('/api/films', function() {
    FilmController::store();
});

// PUT /api/films/{id} - Actualizar película
Router::put('/api/films/{id}', function($id) {
    FilmController::update($id);
});

// DELETE /api/films/{id} - Eliminar película
Router::delete('/api/films/{id}', function($id) {
    FilmController::destroy($id);
});

// ============ RUTAS API DE ACTORES ============
// GET /api/actors - Obtener todos los actores (con paginación y búsqueda)
Router::get('/api/actors', function() {
    ActorController::index();
});

// GET /api/actors/{id} - Obtener actor por ID
Router::get('/api/actors/{id}', function($id) {
    ActorController::show($id);
});

// GET /api/actors/{id}/films - Obtener películas de un actor
Router::get('/api/actors/{id}/films', function($id) {
    ActorController::getFilms($id);
});

// POST /api/actors - Crear nuevo actor
Router::post('/api/actors', function() {
    ActorController::store();
});

// PUT /api/actors/{id} - Actualizar actor
Router::put('/api/actors/{id}', function($id) {
    ActorController::update($id);
});

// DELETE /api/actors/{id} - Eliminar actor
Router::delete('/api/actors/{id}', function($id) {
    ActorController::destroy($id);
});

// ============ RUTAS API DE CLIENTES ============
// GET /api/customers - Obtener todos los clientes (con paginación y búsqueda)
Router::get('/api/customers', function() {
    CustomerController::index();
});

// GET /api/customers/{id} - Obtener cliente por ID
Router::get('/api/customers/{id}', function($id) {
    CustomerController::show($id);
});

// GET /api/customers/{id}/rentals - Obtener historial de rentals de un cliente
Router::get('/api/customers/{id}/rentals', function($id) {
    CustomerController::getRentals($id);
});

// POST /api/customers - Crear nuevo cliente
Router::post('/api/customers', function() {
    CustomerController::store();
});

// PUT /api/customers/{id} - Actualizar cliente
Router::put('/api/customers/{id}', function($id) {
    CustomerController::update($id);
});

// DELETE /api/customers/{id} - Eliminar cliente
Router::delete('/api/customers/{id}', function($id) {
    CustomerController::destroy($id);
});

// ============ RUTA DE INFORMACIÓN DE LA API ============
Router::get('/api', function() {
    $info = [
        'api_name' => 'Sakila API',
        'version' => '1.0.0',
        'description' => 'API REST para la base de datos Sakila',
        'endpoints' => [
            'films' => [
                'GET /api/films' => 'Obtener todas las películas',
                'GET /api/films/{id}' => 'Obtener película por ID',
                'POST /api/films' => 'Crear nueva película',
                'PUT /api/films/{id}' => 'Actualizar película',
                'DELETE /api/films/{id}' => 'Eliminar película'
            ],
            'actors' => [
                'GET /api/actors' => 'Obtener todos los actores',
                'GET /api/actors/{id}' => 'Obtener actor por ID',
                'GET /api/actors/{id}/films' => 'Obtener películas de un actor',
                'POST /api/actors' => 'Crear nuevo actor',
                'PUT /api/actors/{id}' => 'Actualizar actor',
                'DELETE /api/actors/{id}' => 'Eliminar actor'
            ],
            'customers' => [
                'GET /api/customers' => 'Obtener todos los clientes',
                'GET /api/customers/{id}' => 'Obtener cliente por ID',
                'GET /api/customers/{id}/rentals' => 'Obtener historial de rentals',
                'POST /api/customers' => 'Crear nuevo cliente',
                'PUT /api/customers/{id}' => 'Actualizar cliente',
                'DELETE /api/customers/{id}' => 'Eliminar cliente'
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
