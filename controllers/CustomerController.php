<?php
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../config/Config.php';

/**
 * Controlador Customer - Maneja las operaciones CRUD de clientes
 */
class CustomerController {
    
    /**
     * GET - Obtener todos los clientes o buscar
     */
    public static function index() {
        try {
            $customer = new Customer();
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $search = isset($_GET['search']) ? Config::sanitizeInput($_GET['search']) : null;
            
            if ($search) {
                $customers = $customer->search($search);
                Config::sendResponse(200, $customers, "Resultados de búsqueda para: {$search}");
            } else {
                $customers = $customer->readAll($page, $limit);
                $total = $customer->getTotalCount();
                $totalPages = ceil($total / $limit);
                
                $response = [
                    'customers' => $customers,
                    'pagination' => [
                        'current_page' => $page,
                        'total_pages' => $totalPages,
                        'total_items' => $total,
                        'items_per_page' => $limit
                    ]
                ];
                
                Config::sendResponse(200, $response, "Clientes obtenidos correctamente");
            }
        } catch (Exception $e) {
            Config::sendResponse(500, null, "Error interno del servidor: " . $e->getMessage());
        }
    }
    
    /**
     * GET - Obtener cliente por ID
     */
    public static function show($id) {
        try {
            $customer = new Customer();
            $customer->customer_id = $id;
            
            $result = $customer->readOne();
            if ($result) {
                Config::sendResponse(200, $result, "Cliente encontrado");
            } else {
                Config::sendResponse(404, null, "Cliente no encontrado");
            }
        } catch (Exception $e) {
            Config::sendResponse(500, null, "Error interno del servidor: " . $e->getMessage());
        }
    }
    
    /**
     * GET - Obtener historial de rentals de un cliente
     */
    public static function getRentals($id) {
        try {
            $customer = new Customer();
            $customer->customer_id = $id;
            
            // Verificar que el cliente existe
            if (!$customer->readOne()) {
                Config::sendResponse(404, null, "Cliente no encontrado");
            }
            
            $rentals = $customer->getRentals();
            Config::sendResponse(200, $rentals, "Historial de rentals obtenido correctamente");
        } catch (Exception $e) {
            Config::sendResponse(500, null, "Error interno del servidor: " . $e->getMessage());
        }
    }
    
    /**
     * POST - Crear nuevo cliente
     */
    public static function store() {
        try {
            $data = Config::getJsonInput();
            
            // Validar campos requeridos
            $required_fields = ['store_id', 'first_name', 'last_name', 'email', 'address_id'];
            foreach ($required_fields as $field) {
                if (!isset($data[$field]) || empty(trim($data[$field]))) {
                    Config::sendResponse(400, null, "El campo {$field} es requerido");
                }
            }
            
            // Validar email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                Config::sendResponse(400, null, "El email no es válido");
            }
            
            $customer = new Customer();
            $customer->email = Config::sanitizeInput($data['email']);
            $customer->customer_id = 0; // Para verificación de email existente
            
            // Verificar si el email ya existe
            if ($customer->emailExists()) {
                Config::sendResponse(400, null, "El email ya está en uso");
            }
            
            $customer->store_id = (int)$data['store_id'];
            $customer->first_name = Config::sanitizeInput($data['first_name']);
            $customer->last_name = Config::sanitizeInput($data['last_name']);
            $customer->address_id = (int)$data['address_id'];
            $customer->active = isset($data['active']) ? (bool)$data['active'] : true;
            
            if ($customer->create()) {
                Config::sendResponse(201, ['customer_id' => $customer->customer_id], "Cliente creado correctamente");
            } else {
                Config::sendResponse(500, null, "Error al crear el cliente");
            }
        } catch (Exception $e) {
            Config::sendResponse(500, null, "Error interno del servidor: " . $e->getMessage());
        }
    }
    
    /**
     * PUT - Actualizar cliente
     */
    public static function update($id) {
        try {
            $data = Config::getJsonInput();
            
            $customer = new Customer();
            $customer->customer_id = $id;
            
            // Verificar que el cliente existe
            if (!$customer->readOne()) {
                Config::sendResponse(404, null, "Cliente no encontrado");
            }
            
            // Validar email si se proporciona
            if (isset($data['email'])) {
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    Config::sendResponse(400, null, "El email no es válido");
                }
                
                $tempCustomer = new Customer();
                $tempCustomer->email = Config::sanitizeInput($data['email']);
                $tempCustomer->customer_id = $id;
                
                if ($tempCustomer->emailExists()) {
                    Config::sendResponse(400, null, "El email ya está en uso por otro cliente");
                }
                
                $customer->email = Config::sanitizeInput($data['email']);
            }
            
            // Actualizar solo los campos proporcionados
            if (isset($data['store_id'])) $customer->store_id = (int)$data['store_id'];
            if (isset($data['first_name'])) {
                if (empty(trim($data['first_name']))) {
                    Config::sendResponse(400, null, "El campo first_name no puede estar vacío");
                }
                $customer->first_name = Config::sanitizeInput($data['first_name']);
            }
            if (isset($data['last_name'])) {
                if (empty(trim($data['last_name']))) {
                    Config::sendResponse(400, null, "El campo last_name no puede estar vacío");
                }
                $customer->last_name = Config::sanitizeInput($data['last_name']);
            }
            if (isset($data['address_id'])) $customer->address_id = (int)$data['address_id'];
            if (isset($data['active'])) $customer->active = (bool)$data['active'];
            
            if ($customer->update()) {
                Config::sendResponse(200, null, "Cliente actualizado correctamente");
            } else {
                Config::sendResponse(500, null, "Error al actualizar el cliente");
            }
        } catch (Exception $e) {
            Config::sendResponse(500, null, "Error interno del servidor: " . $e->getMessage());
        }
    }
    
    /**
     * DELETE - Eliminar cliente
     */
    public static function destroy($id) {
        try {
            $customer = new Customer();
            $customer->customer_id = $id;
            
            // Verificar que el cliente existe
            if (!$customer->readOne()) {
                Config::sendResponse(404, null, "Cliente no encontrado");
            }
            
            if ($customer->delete()) {
                Config::sendResponse(200, null, "Cliente eliminado correctamente");
            } else {
                Config::sendResponse(400, null, "No se puede eliminar el cliente porque tiene rentals asociados");
            }
        } catch (Exception $e) {
            Config::sendResponse(500, null, "Error interno del servidor: " . $e->getMessage());
        }
    }
}
?>
