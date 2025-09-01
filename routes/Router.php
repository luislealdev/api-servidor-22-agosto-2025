<?php
/**
 * Router - Sistema de rutas para la API
 */
class Router
{
    private static $routes = [];

    /**
     * Registrar ruta GET
     */
    public static function get($pattern, $callback)
    {
        self::$routes['GET'][$pattern] = $callback;
    }

    /**
     * Registrar ruta POST
     */
    public static function post($pattern, $callback)
    {
        self::$routes['POST'][$pattern] = $callback;
    }

    /**
     * Registrar ruta PUT
     */
    public static function put($pattern, $callback)
    {
        self::$routes['PUT'][$pattern] = $callback;
    }

    /**
     * Registrar ruta DELETE
     */
    public static function delete($pattern, $callback)
    {
        self::$routes['DELETE'][$pattern] = $callback;
    }

    /**
     * Procesar la petición y ejecutar la ruta correspondiente
     */
    public static function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        // Remover query string
        $uri = strtok($uri, '?');

        // Remover el directorio base del proyecto
        $basePath = '/leal';
        // $basePath = '/api-servidor-22-agosto-2025';
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        // Asegurar que comience con /
        if ($uri === '' || $uri[0] !== '/') {
            $uri = '/' . $uri;
        }

        // Buscar coincidencia en las rutas registradas
        if (isset(self::$routes[$method])) {
            foreach (self::$routes[$method] as $pattern => $callback) {
                if (self::matchRoute($pattern, $uri, $matches)) {
                    // Ejecutar el callback con los parámetros capturados
                    return call_user_func_array($callback, $matches);
                }
            }
        }

        // Si no se encuentra la ruta
        Config::sendResponse(404, null, "Endpoint no encontrado");
    }

    /**
     * Verificar si una ruta coincide con el patrón
     */
    private static function matchRoute($pattern, $uri, &$matches)
    {
        // Convertir patrón a regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            // Remover la coincidencia completa
            array_shift($matches);
            return true;
        }

        return false;
    }
}
?>