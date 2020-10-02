<?php

class Tickets extends Mysql {

    /**
     * Limit of fetch tickets
     */
    const TICKETS_FETCH_LIMIT = 5;

    /**
     * Current page
     * @var int
     */
    public $currentPage = 1;

    /**
     * Number of page
     * @var int
     */
    public $pageCount;

    /**
     * Create ticket
     * @param $title
     * @param $chapter
     * @param $content
     * @param $authorId
     * @return string
     */
    public function createTicket($title, $chapter, $content, $authorId) {
        /** @var PDO $pdo **/
        $pdo = Mysql::_getConnection();

        // Check if chapter exist
        $req = $pdo->prepare("SELECT * FROM tickets WHERE chapter = ?");
        $req->execute(array($chapter));
        if ($req->rowCount() === 0) {
            // Create chapter
            $req = Mysql::_getConnection()->prepare("INSERT INTO tickets (title, chapter, content, author_id, date_public) VALUES (?, ?, ?, ?, ?)");
            $req->execute(array($title, $chapter, $content, (int)$authorId, time()));
        } else {
            return "Le chapitre <b>" . $chapter . "</b> existe déjà.<br/>Editer le chapitre juste <a href='#'>ici</a>";
        }
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
     * Fetch tickets
     * @return mixed
     */
    public function fetchTickets() {
        $ticketsCount = $this->_ticketsCount();
        $this->pageCount = ceil($ticketsCount / self::TICKETS_FETCH_LIMIT);
        $firstFetch = ($this->currentPage - 1 ) * self::TICKETS_FETCH_LIMIT;
        $req = Mysql::_getConnection()->prepare("SELECT * FROM tickets ORDER BY date_public DESC LIMIT " . $firstFetch . ", " . self::TICKETS_FETCH_LIMIT);
        $req->execute();
        return $req->fetchAll();
    }

    /**
     * Set current page
     * @param $page
     */
    public function setPage($page)
    {
        if ($page > $this->pageCount) {
            $this->currentPage = $this->pageCount;
        } else {
            $this->currentPage = $page;
        }
    }

    /**
     * Count tickets
     * @return int
     */
    protected function _ticketsCount()
    {
        $req = Mysql::_getConnection()->prepare("SELECT * FROM tickets");
        $req->execute();
        return $req->rowCount();
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

    /**
     * Set view on ticket
     * @param $userId
     * @param $ticketId
     */
    public function setView($userId, $ticketId)
    {
        /** @var PDO $pdo */
        $pdo = Mysql::_getConnection();

        // Check if user have already view chapter
        $req = $pdo->prepare("SELECT * FROM views WHERE user_id = ? AND ticket_id = ?");
        $req->execute(array((int)$userId, (int)$ticketId));

        // If not already read, insert in database
        if ($req->rowCount() === 0) {
            $req = Mysql::_getConnection()->prepare("INSERT INTO views (user_id, ticket_id) VALUES (?, ?)");
            $req->execute(array((int)$userId, (int)$ticketId));
        }
    }

}