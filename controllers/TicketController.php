<?php
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
