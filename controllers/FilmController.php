<?php
require_once __DIR__ . '/../models/Film.php';
require_once __DIR__ . '/../config/Config.php';

/**
 * Controlador Film - Maneja las operaciones CRUD de películas
 */
class FilmController {
    
    /**
     * GET - Obtener todas las películas o buscar por título
     */
    public static function index() {
        try {
            $film = new Film();
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $search = isset($_GET['search']) ? Config::sanitizeInput($_GET['search']) : null;
            
            if ($search) {
                $films = $film->searchByTitle($search);
                Config::sendResponse(200, $films, "Resultados de búsqueda para: {$search}");
            } else {
                $films = $film->readAll($page, $limit);
                $total = $film->getTotalCount();
                $totalPages = ceil($total / $limit);
                
                $response = [
                    'films' => $films,
                    'pagination' => [
                        'current_page' => $page,
                        'total_pages' => $totalPages,
                        'total_items' => $total,
                        'items_per_page' => $limit
                    ]
                ];
                
                Config::sendResponse(200, $response, "Películas obtenidas correctamente");
            }
        } catch (Exception $e) {
            Config::sendResponse(500, null, "Error interno del servidor: " . $e->getMessage());
        }
    }
    
    /**
     * GET - Obtener película por ID
     */
    public static function show($id) {
        try {
            $film = new Film();
            $film->film_id = $id;
            
            $result = $film->readOne();
            if ($result) {
                Config::sendResponse(200, $result, "Película encontrada");
            } else {
                Config::sendResponse(404, null, "Película no encontrada");
            }
        } catch (Exception $e) {
            Config::sendResponse(500, null, "Error interno del servidor: " . $e->getMessage());
        }
    }
    
    /**
     * POST - Crear nueva película
     */
    public static function store() {
        try {
            $data = Config::getJsonInput();
            
            // Validar campos requeridos
            $required_fields = ['title', 'language_id'];
            foreach ($required_fields as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    Config::sendResponse(400, null, "El campo {$field} es requerido");
                }
            }
            
            $film = new Film();
            $film->title = Config::sanitizeInput($data['title']);
            $film->description = isset($data['description']) ? Config::sanitizeInput($data['description']) : null;
            $film->release_year = isset($data['release_year']) ? (int)$data['release_year'] : null;
            $film->language_id = (int)$data['language_id'];
            $film->rental_duration = isset($data['rental_duration']) ? (int)$data['rental_duration'] : 3;
            $film->rental_rate = isset($data['rental_rate']) ? (float)$data['rental_rate'] : 4.99;
            $film->length = isset($data['length']) ? (int)$data['length'] : null;
            $film->replacement_cost = isset($data['replacement_cost']) ? (float)$data['replacement_cost'] : 19.99;
            $film->rating = isset($data['rating']) ? Config::sanitizeInput($data['rating']) : 'G';
            $film->special_features = isset($data['special_features']) ? Config::sanitizeInput($data['special_features']) : null;
            
            if ($film->create()) {
                Config::sendResponse(201, ['film_id' => $film->film_id], "Película creada correctamente");
            } else {
                Config::sendResponse(500, null, "Error al crear la película");
            }
        } catch (Exception $e) {
            Config::sendResponse(500, null, "Error interno del servidor: " . $e->getMessage());
        }
    }
    
    /**
     * PUT - Actualizar película
     */
    public static function update($id) {
        try {
            $data = Config::getJsonInput();
            
            $film = new Film();
            $film->film_id = $id;
            
            // Verificar que la película existe
            if (!$film->readOne()) {
                Config::sendResponse(404, null, "Película no encontrada");
            }
            
            // Actualizar solo los campos proporcionados
            if (isset($data['title'])) $film->title = Config::sanitizeInput($data['title']);
            if (isset($data['description'])) $film->description = Config::sanitizeInput($data['description']);
            if (isset($data['release_year'])) $film->release_year = (int)$data['release_year'];
            if (isset($data['language_id'])) $film->language_id = (int)$data['language_id'];
            if (isset($data['rental_duration'])) $film->rental_duration = (int)$data['rental_duration'];
            if (isset($data['rental_rate'])) $film->rental_rate = (float)$data['rental_rate'];
            if (isset($data['length'])) $film->length = (int)$data['length'];
            if (isset($data['replacement_cost'])) $film->replacement_cost = (float)$data['replacement_cost'];
            if (isset($data['rating'])) $film->rating = Config::sanitizeInput($data['rating']);
            if (isset($data['special_features'])) $film->special_features = Config::sanitizeInput($data['special_features']);
            
            if ($film->update()) {
                Config::sendResponse(200, null, "Película actualizada correctamente");
            } else {
                Config::sendResponse(500, null, "Error al actualizar la película");
            }
        } catch (Exception $e) {
            Config::sendResponse(500, null, "Error interno del servidor: " . $e->getMessage());
        }
    }
    
    /**
     * DELETE - Eliminar película
     */
    public static function destroy($id) {
        try {
            $film = new Film();
            $film->film_id = $id;
            
            // Verificar que la película existe
            if (!$film->readOne()) {
                Config::sendResponse(404, null, "Película no encontrada");
            }
            
            if ($film->delete()) {
                Config::sendResponse(200, null, "Película eliminada correctamente");
            } else {
                Config::sendResponse(500, null, "Error al eliminar la película. Puede que tenga referencias en otras tablas.");
            }
        } catch (Exception $e) {
            Config::sendResponse(500, null, "Error interno del servidor: " . $e->getMessage());
        }
    }
}
?>
