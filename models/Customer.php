<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * Modelo Customer - Gestión de clientes
 * Basado en la tabla customer de Sakila DB
 */
class Customer {
    private $conn;
    private $table = 'customer';

    // Propiedades basadas en la tabla customer
    public $customer_id;
    public $store_id;
    public $first_name;
    public $last_name;
    public $email;
    public $address_id;
    public $active;
    public $create_date;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    /**
     * CREATE - Crear nuevo cliente
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET store_id = :store_id,
                      first_name = :first_name, 
                      last_name = :last_name,
                      email = :email,
                      address_id = :address_id,
                      active = :active,
                      create_date = NOW()";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':store_id', $this->store_id);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':address_id', $this->address_id);
        $stmt->bindParam(':active', $this->active);
        
        if ($stmt->execute()) {
            $this->customer_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * READ - Obtener todos los clientes con paginación
     */
    public function readAll($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT c.customer_id, c.store_id, c.first_name, c.last_name, 
                         c.email, c.active, c.create_date,
                         CONCAT(c.first_name, ' ', c.last_name) as full_name,
                         a.address, ci.city, co.country
                  FROM " . $this->table . " c
                  LEFT JOIN address a ON c.address_id = a.address_id
                  LEFT JOIN city ci ON a.city_id = ci.city_id
                  LEFT JOIN country co ON ci.country_id = co.country_id
                  ORDER BY c.last_name, c.first_name
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * READ - Obtener cliente por ID
     */
    public function readOne() {
        $query = "SELECT c.*, 
                         CONCAT(c.first_name, ' ', c.last_name) as full_name,
                         a.address, a.postal_code, a.phone,
                         ci.city, co.country
                  FROM " . $this->table . " c
                  LEFT JOIN address a ON c.address_id = a.address_id
                  LEFT JOIN city ci ON a.city_id = ci.city_id
                  LEFT JOIN country co ON ci.country_id = co.country_id
                  WHERE c.customer_id = :customer_id 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_id', $this->customer_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->store_id = $row['store_id'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->email = $row['email'];
            $this->address_id = $row['address_id'];
            $this->active = $row['active'];
            $this->create_date = $row['create_date'];
            return $row;
        }
        return false;
    }

    /**
     * UPDATE - Actualizar cliente
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET store_id = :store_id,
                      first_name = :first_name,
                      last_name = :last_name,
                      email = :email,
                      address_id = :address_id,
                      active = :active
                  WHERE customer_id = :customer_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':customer_id', $this->customer_id);
        $stmt->bindParam(':store_id', $this->store_id);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':address_id', $this->address_id);
        $stmt->bindParam(':active', $this->active);
        
        return $stmt->execute();
    }

    /**
     * DELETE - Eliminar cliente (solo si no tiene rentals)
     */
    public function delete() {
        // Primero verificar si tiene rentals
        $checkQuery = "SELECT COUNT(*) as rental_count FROM rental WHERE customer_id = :customer_id";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(':customer_id', $this->customer_id);
        $checkStmt->execute();
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['rental_count'] > 0) {
            return false; // No se puede eliminar si tiene rentals
        }
        
        $query = "DELETE FROM " . $this->table . " WHERE customer_id = :customer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_id', $this->customer_id);
        return $stmt->execute();
    }

    /**
     * Buscar clientes por nombre o email
     */
    public function search($searchTerm) {
        $query = "SELECT c.customer_id, c.store_id, c.first_name, c.last_name, 
                         c.email, c.active, c.create_date,
                         CONCAT(c.first_name, ' ', c.last_name) as full_name,
                         a.address, ci.city, co.country
                  FROM " . $this->table . " c
                  LEFT JOIN address a ON c.address_id = a.address_id
                  LEFT JOIN city ci ON a.city_id = ci.city_id
                  LEFT JOIN country co ON ci.country_id = co.country_id
                  WHERE c.first_name LIKE :search 
                     OR c.last_name LIKE :search
                     OR c.email LIKE :search
                     OR CONCAT(c.first_name, ' ', c.last_name) LIKE :search
                  ORDER BY c.last_name, c.first_name";
        
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$searchTerm}%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener historial de rentals de un cliente
     */
    public function getRentals() {
        $query = "SELECT r.rental_id, r.rental_date, r.return_date,
                         f.title, f.rental_rate,
                         DATEDIFF(IFNULL(r.return_date, NOW()), r.rental_date) as days_rented
                  FROM rental r
                  INNER JOIN inventory i ON r.inventory_id = i.inventory_id
                  INNER JOIN film f ON i.film_id = f.film_id
                  WHERE r.customer_id = :customer_id
                  ORDER BY r.rental_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_id', $this->customer_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener total de clientes (para paginación)
     */
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Verificar si el email ya existe
     */
    public function emailExists() {
        $query = "SELECT customer_id FROM " . $this->table . " 
                  WHERE email = :email AND customer_id != :customer_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':customer_id', $this->customer_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
}
?>
