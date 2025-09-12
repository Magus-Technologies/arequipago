<?php

class Route {

    public function __construct() {
        
    }

    private static $middleware = array();
    private static $uris = array();
    private static $functionBase;
    private static $middlewareBase;

    public static function baseStatic($funcBase,$middlewareBase=[]) {
        Route::$functionBase=$funcBase;
        Route::$middlewareBase=$middlewareBase;
    }
    public static function postBase($uri, $function = null){
        Route::add("GET", $uri, Route::$functionBase)->Middleware( Route::$middlewareBase);
        Route::add("POST", $uri, $function)->Middleware( Route::$middlewareBase);
    }
    public static function add($method, $uri, $function = null) {
        Route::$uris[] = new Uri(self::parseUri($uri), $method, $function);
        //Retornará un Middleware...
        return Route::$uris[count(Route::$uris)-1];
    }

    public static function get($uri, $function = null) {
        return Route::add("GET", $uri, $function);
    }

    public static function post($uri, $function = null) {
        return Route::add("POST", $uri, $function);
    }

    public static function put($uri, $function = null) {
        return Route::add("PUT", $uri, $function);
    }

    public static function delete($uri, $function = null) {
        return Route::add("DELETE", $uri, $function);
    }

    public static function any($uri, $function = null) {
        return Route::add("ANY", $uri, $function);
    }

    private static function parseUri($uri) {
        $uri = trim($uri, '/');
        $uri = (strlen($uri) > 0) ? $uri : '/';
        return $uri;
    }

    public static function submit() {
        // Verifica la inactividad del usuario
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > INACTIVITY_TIMEOUT_SECONDS)) {
            // La sesión ha expirado por inactividad
            session_unset();     // Limpia todas las variables de sesión
            session_destroy();   // Destruye la sesión
            $_SESSION = array(); // Vacía la superglobal $_SESSION en el script actual
            unset($_SESSION['token']); // Elimina el token de autenticación

            // Redirige al usuario directamente a la página de login con un parámetro para limpiar localStorage
            header("Location: /arequipago/login?logout_local=true"); // Redirige directamente al login
            exit(); // Es crucial terminar la ejecución aquí
        }

        // Actualiza la marca de tiempo de la última actividad en cada solicitud válida
        $_SESSION['last_activity'] = time();

        $method = $_SERVER['REQUEST_METHOD'];
        $uri = isset($_GET['uri']) ? $_GET['uri'] : '';
        $uri = self::parseUri($uri);

        //Verifica si la uri que está pidiendo el usuario se encuentra registrada...
        foreach (Route::$uris as $key => $recordUri) {
            if ($recordUri->match($uri)) {
                return $recordUri->call();
            }
        }

        //Muestra el mensaje de error 404...
        header("Content-Type: text/html");
        echo (new View())->render("404");
        //echo 'La uri (<a href="' . $uri . '">' . $uri . '</a>) no se encuentra regiostrada en el método ' . $method . '.';
        return '';
    }

}
