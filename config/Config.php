<?php
/**
 * Configuración general de la API
 */
class Config {
    // Configuración de Headers CORS
    public static function setCorsHeaders() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }

    // Configuración de respuestas de error
    public static function sendResponse($statusCode, $data = null, $message = null) {
        http_response_code($statusCode);
        
        $response = [];
        if ($message) {
            $response['message'] = $message;
        }
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Validar datos de entrada JSON
    public static function getJsonInput() {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            self::sendResponse(400, null, "JSON inválido");
        }
        
        return $data;
    }

    // Sanitizar entrada de datos
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)));
    }
}
?>
