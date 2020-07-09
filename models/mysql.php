<?php

class mysql {

    /**
     * Connection with database
     * @var PDO
     */
    private $_pdo;

    /**
     * Login flag
     * @var bool
     */
    protected $_isLogin = false;

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
    private function _initSession() {
        session_start();
    }

    /**
     * Get connection with database
     * @return PDO
     */
    protected function _getConnection() {
        return $this->_pdo;
    }

    /**
     * Get login flag
     * @return bool
     */
    protected function _getIsLogin() {
        return $this->_isLogin;
    }

    /**
     * Set login flag
     * @param $flag
     * @param $informations
     */
    protected function _setIsLogin($flag, $informations) {
        $this->_isLogin = $flag;
        $_SESSION['user'] = [
            'id' => $informations[0],
            'email' => $informations[1],
            'username' => $informations[2],
            'register_date' => $informations[3]
        ];
    }
}