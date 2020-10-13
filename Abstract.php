<?php

// Require Mysql class
require_once __DIR__ . '/Models/Mysql.php';

/**
 * Class Core_Abstract
 */
class Core_Abstract extends Mysql
{

    /**
     * Host (domain)
     * @var string
     */
    private $_host;

    /**
     * Get url with GET_METHOD
     * @return string
     */
    public function getUrl()
    {
        if (isset($_GET['url'])) {
            return $_GET['url'];
        }
        return '/';
    }

    /**
     * Set host
     */
    private function _initHost()
    {
        parent::__construct();
        $req = Mysql::_getConnection()->prepare("SELECT * FROM config");
        $req->execute();
        $config = $req->fetchAll();
        $this->_host =  $config[1]['value'] != NULL ? $config[1]['value'] : $config[0]['value'];
    }

    /**
     * Get host
     * @return string|null
     */
    public function getHost()
    {
        if (!$this->_host) {
            $this->_initHost();
        }
        return $this->_host;
    }

    /**
     * Try to connect with cookie
     */
    public function tryConnectionWithCookie()
    {
        if (isset($_COOKIE['remember_key'])
            && !empty($_COOKIE['remember_key'])) {

            // Get remember key
            $rememberKey = $_COOKIE['remember_key'];

            $req = Mysql::_getConnection()->prepare("SELECT * FROM remember_me WHERE remember_key = ?");
            $req->execute(array($rememberKey));
            if ($req->rowCount() > 0) {

                // Get user ID
                $result = $req->fetch();
                $userId = $result['user_id'];

                // Get user information
                $req = Mysql::_getConnection()->prepare("SELECT * FROM users WHERE id = ?");
                $req->execute(array($userId));
                if ($req->rowCount() > 0) {

                    $user = $req->fetch();

                    // Set login
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'email' => $user['email'],
                        'username' => $user['username'],
                        'register_date' => $user['register_date']
                    ];
                }
            }
        }
    }
}