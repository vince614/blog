<?php

class Tickets extends Mysql {

    /**
     * Create ticket
     * @param $ticketInformation
     * @param $author
     */
    public function createTicket($ticketInformation, $author) {
        $req = parent::_getConnection()->prepare("INSERT INTO tickets (title, content, author, date_public) VALUES (?, ?, ?, ?)");
        $req->execute(array($ticketInformation[0], $ticketInformation[1], $author, time()));
    }

    /**
     * Read ticket
     * @param $ticketId
     * @return mixed
     */
    public function readTicket($ticketId) {
        $req = parent::_getConnection()->prepare("SELECT * FROM tickets WHERE id = ?");
        $req->execute(array($ticketId));
        return $req->fetch();
    }

    /**
     * Update ticket
     * @param $ticketInformation
     * @param $ticketId
     * @return tickets
     */
    public function updateTicket($ticketInformation, $ticketId) {
        $req = parent::_getConnection()->prepare("UPDATE tickets SET title = ?, content = ? WHERE id = ?");
        $req->execute(array($ticketInformation[0], $ticketInformation[1], $ticketId));
        return $this;
    }

    /**
     * Delete ticket
     * @param $ticketId
     * @return tickets
     */
    public function deleteTicket($ticketId) {
        $req = parent::_getConnection()->prepare('DELETE FROM tickets WHERE id = ?');
        $req->execute(array($ticketId));
        return $this;
    }

}