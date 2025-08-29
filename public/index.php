<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/Ticket.php';
require_once __DIR__ . '/../models/Attachment.php';
require_once __DIR__ . '/../controllers/TicketController.php';

// Determine the base path dynamically
$basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);

// Get the request URI and remove the base path to get the route
$requestUri = strtok($_SERVER['REQUEST_URI'], '?');
$routePath = substr($requestUri, strlen($basePath));

// Parse the route
$uri = explode('/', trim($routePath, '/'));

// Determine controller, method, and param
$controllerSegment = !empty($uri[0]) ? $uri[0] : 'ticket';
$controllerName = ucfirst($controllerSegment) . 'Controller';
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
