<?php

class mysql {

    /**
     * Connection with database
     * @var PDO
     */
    private $_pdo;

    /**
     * Mysql constructor.
     */
    public function __construct()
    {
        if ($this->_pdo === null) {
            $this->_pdo = new PDO('mysql:host=localhost;dbname=blog;charset=utf8', 'root', '');
        }
    }

    /**
     * Get connection with database
     * @return PDO
     */
    protected function _getConnection() {
        return $this->_pdo;
    }
}