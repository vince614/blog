<?php

class Mysql
{

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

        // If session is not init, init it
        if (!isset($_SESSION)) {
            $this->_initSession();
        }
    }

    /**
     * Init session
     */
    private function _initSession()
    {
        session_start();
    }

    /**
     * Get connection with database
     * @return PDO
     */
    protected function _getConnection()
    {
        return $this->_pdo;
    }

    /**
     * Set login flag
     * @param $informations
     */
    protected function _setIsLogin($informations)
    {
        $_SESSION['user'] = [
            'id' => $informations[0],
            'email' => $informations[1],
            'username' => $informations[2],
            'register_date' => $informations[3]
        ];
    }

    /**
     * Generate random key
     * @param $lenght
     * @return string
     */
    protected function _generateRememberKey($lenght)
    {
        $randomPseudoBytes = openssl_random_pseudo_bytes($lenght);
        return base64_encode($randomPseudoBytes);
    }
}