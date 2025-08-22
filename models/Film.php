<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * Modelo Film - Gestión de películas
 * Basado en la tabla film de Sakila DB
 */
class Film {
    private $conn;
    private $table = 'film';

    // Propiedades basadas en la tabla film
    public $film_id;
    public $title;
    public $description;
    public $release_year;
    public $language_id;
    public $rental_duration;
    public $rental_rate;
    public $length;
    public $replacement_cost;
    public $rating;
    public $special_features;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    /**
     * CREATE - Crear nueva película
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET title = :title, 
                      description = :description, 
                      release_year = :release_year,
                      language_id = :language_id,
                      rental_duration = :rental_duration,
                      rental_rate = :rental_rate,
                      length = :length,
                      replacement_cost = :replacement_cost,
                      rating = :rating,
                      special_features = :special_features";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':release_year', $this->release_year);
        $stmt->bindParam(':language_id', $this->language_id);
        $stmt->bindParam(':rental_duration', $this->rental_duration);
        $stmt->bindParam(':rental_rate', $this->rental_rate);
        $stmt->bindParam(':length', $this->length);
        $stmt->bindParam(':replacement_cost', $this->replacement_cost);
        $stmt->bindParam(':rating', $this->rating);
        $stmt->bindParam(':special_features', $this->special_features);
        
        if ($stmt->execute()) {
            $this->film_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * READ - Obtener todas las películas con paginación
     */
    public function readAll($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT f.film_id, f.title, f.description, f.release_year, 
                         f.rental_rate, f.length, f.rating, l.name as language
                  FROM " . $this->table . " f
                  LEFT JOIN language l ON f.language_id = l.language_id
                  ORDER BY f.title
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * READ - Obtener película por ID
     */
    public function readOne() {
        $query = "SELECT f.*, l.name as language_name
                  FROM " . $this->table . " f
                  LEFT JOIN language l ON f.language_id = l.language_id
                  WHERE f.film_id = :film_id
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':film_id', $this->film_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->release_year = $row['release_year'];
            $this->language_id = $row['language_id'];
            $this->rental_duration = $row['rental_duration'];
            $this->rental_rate = $row['rental_rate'];
            $this->length = $row['length'];
            $this->replacement_cost = $row['replacement_cost'];
            $this->rating = $row['rating'];
            $this->special_features = $row['special_features'];
            return $row;
        }
        return false;
    }

    /**
     * UPDATE - Actualizar película
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET title = :title,
                      description = :description,
                      release_year = :release_year,
                      language_id = :language_id,
                      rental_duration = :rental_duration,
                      rental_rate = :rental_rate,
                      length = :length,
                      replacement_cost = :replacement_cost,
                      rating = :rating,
                      special_features = :special_features
                  WHERE film_id = :film_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':film_id', $this->film_id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':release_year', $this->release_year);
        $stmt->bindParam(':language_id', $this->language_id);
        $stmt->bindParam(':rental_duration', $this->rental_duration);
        $stmt->bindParam(':rental_rate', $this->rental_rate);
        $stmt->bindParam(':length', $this->length);
        $stmt->bindParam(':replacement_cost', $this->replacement_cost);
        $stmt->bindParam(':rating', $this->rating);
        $stmt->bindParam(':special_features', $this->special_features);
        
        return $stmt->execute();
    }

    /**
     * DELETE - Eliminar película
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE film_id = :film_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':film_id', $this->film_id);
        return $stmt->execute();
    }

    /**
     * Buscar películas por título
     */
    public function searchByTitle($searchTerm) {
        $query = "SELECT f.film_id, f.title, f.description, f.release_year, 
                         f.rental_rate, f.length, f.rating, l.name as language
                  FROM " . $this->table . " f
                  LEFT JOIN language l ON f.language_id = l.language_id
                  WHERE f.title LIKE :search
                  ORDER BY f.title";
        
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$searchTerm}%";
        $stmt->bindParam(':search', $searchTerm);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener total de películas (para paginación)
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
