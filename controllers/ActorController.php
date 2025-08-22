<?php
require_once __DIR__ . '/../models/Actor.php';
require_once __DIR__ . '/../config/Config.php';

/**
 * Controlador Actor - Maneja las operaciones CRUD de actores
 */
class ActorController {
    
    /**
     * GET - Obtener todos los actores o buscar por nombre
     */
    public static function index() {
        try {
            $actor = new Actor();
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $search = isset($_GET['search']) ? Config::sanitizeInput($_GET['search']) : null;
            
            if ($search) {
                $actors = $actor->searchByName($search);
                Config::sendResponse(200, $actors, "Resultados de búsqueda para: {$search}");
            } else {
                $actors = $actor->readAll($page, $limit);
                $total = $actor->getTotalCount();
                $totalPages = ceil($total / $limit);
                
                $response = [
                    'actors' => $actors,
                    'pagination' => [
                        'current_page' => $page,
                        'total_pages' => $totalPages,
                        'total_items' => $total,
                        'items_per_page' => $limit
                    ]
                ];
                
                Config::sendResponse(200, $response, "Actores obtenidos correctamente");
            }
        } catch (Exception $e) {
            Config::sendResponse(500, null, "Error interno del servidor: " . $e->getMessage());
        }
    }
    
    /**
     * GET - Obtener actor por ID
     */
    public static function show($id) {
        try {
            $actor = new Actor();
            $actor->actor_id = $id;
            
            $result = $actor->readOne();
            if ($result) {
                Config::sendResponse(200, $result, "Actor encontrado");
            } else {
                Config::sendResponse(404, null, "Actor no encontrado");
            }
        } catch (Exception $e) {
            Config::sendResponse(500, null, "Error interno del servidor: " . $e->getMessage());
        }
    }
    
    /**
     * GET - Obtener películas de un actor
     */
    public static function getFilms($id) {
        try {
            $actor = new Actor();
            $actor->actor_id = $id;
            
            // Verificar que el actor existe
            if (!$actor->readOne()) {
                Config::sendResponse(404, null, "Actor no encontrado");
            }
            
            $films = $actor->getFilms();
            Config::sendResponse(200, $films, "Películas del actor obtenidas correctamente");
        } catch (Exception $e) {
            Config::sendResponse(500, null, "Error interno del servidor: " . $e->getMessage());
        }
    }
    
    /**
     * POST - Crear nuevo actor
     */
    public static function store() {
        try {
            $data = Config::getJsonInput();
            
            // Validar campos requeridos
            $required_fields = ['first_name', 'last_name'];
            foreach ($required_fields as $field) {
                if (!isset($data[$field]) || empty(trim($data[$field]))) {
                    Config::sendResponse(400, null, "El campo {$field} es requerido");
                }
            }
            
            $actor = new Actor();
            $actor->first_name = Config::sanitizeInput($data['first_name']);
            $actor->last_name = Config::sanitizeInput($data['last_name']);
            
            if ($actor->create()) {
                Config::sendResponse(201, ['actor_id' => $actor->actor_id], "Actor creado correctamente");
            } else {
                Config::sendResponse(500, null, "Error al crear el actor");
            }
        } catch (Exception $e) {
            Config::sendResponse(500, null, "Error interno del servidor: " . $e->getMessage());
        }
    }
    
    /**
     * PUT - Actualizar actor
     */
    public static function update($id) {
        try {
            $data = Config::getJsonInput();
            
            $actor = new Actor();
            $actor->actor_id = $id;
            
            // Verificar que el actor existe
            if (!$actor->readOne()) {
                Config::sendResponse(404, null, "Actor no encontrado");
            }
            
            // Actualizar solo los campos proporcionados
            if (isset($data['first_name'])) {
                if (empty(trim($data['first_name']))) {
                    Config::sendResponse(400, null, "El campo first_name no puede estar vacío");
                }
                $actor->first_name = Config::sanitizeInput($data['first_name']);
            }
            
            if (isset($data['last_name'])) {
                if (empty(trim($data['last_name']))) {
                    Config::sendResponse(400, null, "El campo last_name no puede estar vacío");
                }
                $actor->last_name = Config::sanitizeInput($data['last_name']);
            }
            
            if ($actor->update()) {
                Config::sendResponse(200, null, "Actor actualizado correctamente");
            } else {
                Config::sendResponse(500, null, "Error al actualizar el actor");
            }
        } catch (Exception $e) {
            Config::sendResponse(500, null, "Error interno del servidor: " . $e->getMessage());
        }
    }
    
    /**
     * DELETE - Eliminar actor
     */
    public static function destroy($id) {
        try {
            $actor = new Actor();
            $actor->actor_id = $id;
            
            // Verificar que el actor existe
            if (!$actor->readOne()) {
                Config::sendResponse(404, null, "Actor no encontrado");
            }
            
            if ($actor->delete()) {
                Config::sendResponse(200, null, "Actor eliminado correctamente");
            } else {
                Config::sendResponse(500, null, "Error al eliminar el actor. Puede que tenga referencias en otras tablas.");
            }
        } catch (Exception $e) {
            Config::sendResponse(500, null, "Error interno del servidor: " . $e->getMessage());
        }
    }
}
?>
