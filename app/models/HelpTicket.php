<?php

namespace models;

use PDO;

class HelpTicket {
    private $conn;
    private $table_name = "help_tickets";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createTicket($user_id, $subject, $message) {
        $query = "INSERT INTO " . $this->table_name . " (user_id, subject, message, status) VALUES (:user_id, :subject, :message, 'open')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':message', $message);
        return $stmt->execute();
    }

    public function getAllTickets() {
        $query = "SELECT ht.*, u.username FROM " . $this->table_name . " ht JOIN users u ON ht.user_id = u.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function resolveTicket($ticket_id) {
        $query = "UPDATE " . $this->table_name . " SET status = 'resolved' WHERE id = :ticket_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ticket_id', $ticket_id);
        return $stmt->execute();
    }
}
?>
