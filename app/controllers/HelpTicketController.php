<?php

namespace controllers;

use models\HelpTicket;

class HelpTicketController {
    private $helpTicketModel;

    public function __construct(HelpTicket $helpTicketModel) {
        $this->helpTicketModel = $helpTicketModel;
    }

    public function createTicket($user_id, $subject, $message) {
        return $this->helpTicketModel->createTicket($user_id, $subject, $message);
    }

    public function getAllTickets() {
        return $this->helpTicketModel->getAllTickets();
    }

    public function resolveTicket($ticket_id) {
        return $this->helpTicketModel->resolveTicket($ticket_id);
    }
}
?>
