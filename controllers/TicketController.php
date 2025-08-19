<?php
class TicketController extends BaseController {
    private $ticketModel;
    private $attachmentModel;

    public function __construct() {
        $this->ticketModel = new Ticket();
        $this->attachmentModel = new Attachment();
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
        $ticketId = $this->ticketModel->create($data);

        if ($ticketId && isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'public/uploads/screenshots/';
            $filename = uniqid() . '-' . basename($_FILES['screenshot']['name']);
            $filepath = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['screenshot']['tmp_name'], $filepath)) {
                $attachmentData = [
                    'ticket_id' => $ticketId,
                    'filename' => $filename,
                    'filepath' => $filepath,
                    'mime_type' => $_FILES['screenshot']['type'],
                    'filesize' => $_FILES['screenshot']['size'],
                    'uploaded_by' => $_POST['requested_by']
                ];
                $this->attachmentModel->create($attachmentData);
            }
        }

        header('Location: /tickets');
    }

    public function close($id) {
        $this->ticketModel->updateStatus($id, 'cerrado');
        header('Location: /tickets');
    }
}
