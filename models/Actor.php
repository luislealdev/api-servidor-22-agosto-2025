<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * Modelo Actor - Gestión de actores
 * Basado en la tabla actor de Sakila DB
 */
class Actor {
    private $conn;
    private $table = 'actor';

    // Propiedades basadas en la tabla actor
    public $actor_id;
    public $first_name;
    public $last_name;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    /**
     * CREATE - Crear nuevo actor
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET first_name = :first_name, 
                      last_name = :last_name";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        
        if ($stmt->execute()) {
            $this->actor_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * READ - Obtener todos los actores con paginación
     */
    public function readAll($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT actor_id, first_name, last_name,
                         CONCAT(first_name, ' ', last_name) as full_name
                  FROM " . $this->table . "
                  ORDER BY last_name, first_name
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * READ - Obtener actor por ID
     */
    public function readOne() {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE actor_id = :actor_id 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':actor_id', $this->actor_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            return $row;
        }
        return false;
    }

    /**
     * UPDATE - Actualizar actor
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET first_name = :first_name,
                      last_name = :last_name
                  WHERE actor_id = :actor_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':actor_id', $this->actor_id);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        
        return $stmt->execute();
    }

    /**
     * DELETE - Eliminar actor
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE actor_id = :actor_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':actor_id', $this->actor_id);
        return $stmt->execute();
    }

    /**
     * Buscar actores por nombre
     */
    public function searchByName($searchTerm) {
        $query = "SELECT actor_id, first_name, last_name,
                         CONCAT(first_name, ' ', last_name) as full_name
                  FROM " . $this->table . "
                  WHERE first_name LIKE :search 
                     OR last_name LIKE :search
                     OR CONCAT(first_name, ' ', last_name) LIKE :search
                  ORDER BY last_name, first_name";
        
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$searchTerm}%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener películas de un actor
     */
    public function getFilms() {
        $query = "SELECT f.film_id, f.title, f.release_year, f.rating
                  FROM film f
                  INNER JOIN film_actor fa ON f.film_id = fa.film_id
                  WHERE fa.actor_id = :actor_id
                  ORDER BY f.title";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':actor_id', $this->actor_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener total de actores (para paginación)
     */
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>
