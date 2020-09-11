<?php

class Tickets extends Mysql {

    /**
     * Create ticket
     * @param $title
     * @param $chapter
     * @param $content
     * @param $author
     */
    public function createTicket($title, $chapter, $content, $author) {
        $req = Mysql::_getConnection()->prepare("INSERT INTO tickets (title, content, chapter, author, date_public) VALUES (?, ?, ?, ?, ?)");
        $req->execute(array($title, $chapter, $content, $author, time()));
    }

    /**
     * Read ticket
     * @param $ticketId
     * @return mixed
     */
    public function readTicket($ticketId) {
        $req = Mysql::_getConnection()->prepare("SELECT * FROM tickets WHERE id = ?");
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
        $req = Mysql::_getConnection()->prepare("UPDATE tickets SET title = ?, content = ? WHERE id = ?");
        $req->execute(array($ticketInformation[0], $ticketInformation[1], $ticketId));
        return $this;
    }

    /**
     * Delete ticket
     * @param $ticketId
     * @return tickets
     */
    public function deleteTicket($ticketId) {
        $req = Mysql::_getConnection()->prepare('DELETE FROM tickets WHERE id = ?');
        $req->execute(array($ticketId));
        return $this;
    }

}