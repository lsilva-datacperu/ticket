<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/Ticket.php';
require_once __DIR__ . '/../models/Attachment.php';
require_once __DIR__ . '/../controllers/TicketController.php';

$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$controllerName = ucfirst($uri[0] ?? 'tickets') . 'Controller';
$method = $uri[1] ?? 'index';
$param = $uri[2] ?? null;

if (class_exists($controllerName)) {
    $controller = new $controllerName();
    if (method_exists($controller, $method)) {
        $param ? $controller->$method($param) : $controller->$method();
    } else {
        echo "Método no encontrado.";
    }
} else {
    echo "Controlador no encontrado.";
}
