<?php
class Attachment {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO attachments (ticket_id, filename, filepath, mime_type, filesize, uploaded_by)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['ticket_id'],
            $data['filename'],
            $data['filepath'],
            $data['mime_type'],
            $data['filesize'],
            $data['uploaded_by']
        ]);
    }
}
