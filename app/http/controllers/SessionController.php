<?php
// app/http/controllers/SessionController.php

class SessionController extends Controller
{
    public function keepAlive()
    {
        // Asegurarse de que la sesión ya esté iniciada (debería estarlo por launcher.php)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Solo actualizar si el usuario está logueado
        if (isset($_SESSION['usuario_id'])) {
            $_SESSION['last_activity'] = time();
            error_log("SessionController: keepAlive updated last_activity for user " . $_SESSION['usuario_id']);
            echo json_encode(['status' => 'success', 'message' => 'Session activity updated.']);
        } else {
            // Si no hay usuario_id, la sesión ya expiró o no estaba logueado.
            // Para una solicitud AJAX, un JSON es mejor que una redirección.
            error_log("SessionController: keepAlive called without active user session.");
            echo json_encode(['status' => 'error', 'message' => 'No active session.']);
        }
        exit(); // Es crucial salir para evitar que se renderice una vista
    }
}