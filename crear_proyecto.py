import os

# Diccionario con archivos y su contenido
archivos = {
    "core/Database.php": """<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $host = "localhost";
        $dbname = "ticketing";
        $user = "root";
        $pass = "";

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}
""",

    "core/BaseController.php": """<?php
class BaseController {
    protected function render($view, $data = []) {
        extract($data);
        require __DIR__ . '/../views/' . $view . '.php';
    }
}
""",

    "models/Ticket.php": """<?php
class Ticket {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM tickets ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM tickets WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO tickets (code, title, description, request_type, priority, module_id, requested_by)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['code'],
            $data['title'],
            $data['description'],
            $data['request_type'],
            $data['priority'],
            $data['module_id'],
            $data['requested_by']
        ]);
    }
}
""",

    "controllers/TicketController.php": """<?php
class TicketController extends BaseController {
    private $ticketModel;

    public function __construct() {
        $this->ticketModel = new Ticket();
    }

    public function index() {
        $tickets = $this->ticketModel->getAll();
        $this->render('tickets/index', ['tickets' => $tickets]);
    }

    public function view($id) {
        $ticket = $this->ticketModel->getById($id);
        $this->render('tickets/view', ['ticket' => $ticket]);
    }

    public function createForm() {
        $this->render('tickets/create');
    }

    public function store() {
        $data = [
            'code' => 'TKT-' . date('Y') . '-' . rand(1000, 9999),
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'request_type' => $_POST['request_type'],
            'priority' => $_POST['priority'],
            'module_id' => $_POST['module_id'],
            'requested_by' => $_POST['requested_by']
        ];
        $this->ticketModel->create($data);
        header('Location: /tickets');
    }
}
""",

    "views/tickets/index.php": """<h1>Listado de Tickets</h1>
<a href="/tickets/create">Nuevo Ticket</a>
<table border="1">
    <tr>
        <th>Código</th>
        <th>Título</th>
        <th>Tipo</th>
        <th>Prioridad</th>
        <th>Estado</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($tickets as $ticket): ?>
        <tr>
            <td><?= htmlspecialchars($ticket['code']) ?></td>
            <td><?= htmlspecialchars($ticket['title']) ?></td>
            <td><?= htmlspecialchars($ticket['request_type']) ?></td>
            <td><?= htmlspecialchars($ticket['priority']) ?></td>
            <td><?= htmlspecialchars($ticket['status']) ?></td>
            <td><a href="/tickets/view/<?= $ticket['id'] ?>">Ver</a></td>
        </tr>
    <?php endforeach; ?>
</table>
""",

    "views/tickets/create.php": """<h1>Crear Ticket</h1>
<form method="POST" action="/tickets/store">
    <label>Título:</label>
    <input type="text" name="title" required><br>

    <label>Descripción:</label>
    <textarea name="description" required></textarea><br>

    <label>Tipo:</label>
    <select name="request_type">
        <option value="mejora">Mejora</option>
        <option value="bug">Bug</option>
        <option value="nueva_funcionalidad">Nueva Funcionalidad</option>
    </select><br>

    <label>Prioridad:</label>
    <select name="priority">
        <option value="baja">Baja</option>
        <option value="media">Media</option>
        <option value="alta">Alta</option>
    </select><br>

    <label>Módulo:</label>
    <input type="number" name="module_id"><br>

    <label>ID Solicitante:</label>
    <input type="number" name="requested_by"><br>

    <button type="submit">Guardar</button>
</form>
""",

    "public/index.php": """<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/Ticket.php';
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
"""
}

# Crear carpetas y archivos
for ruta, contenido in archivos.items():
    carpeta = os.path.dirname(ruta)
    if not os.path.exists(carpeta):
        os.makedirs(carpeta, exist_ok=True)
    with open(ruta, "w", encoding="utf-8") as f:
        f.write(contenido)

print("✅ Estructura MVC generada correctamente.")
