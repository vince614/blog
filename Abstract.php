<?php

// Require Mysql class
require_once __DIR__ . '/Models/Mysql.php';

/**
 * Class Core_Abstract
 */
class Core_Abstract extends Mysql
{
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
     * Get host
     * @return string
     */
    public function getHost()
    {
        $rootDirectory = '/' . explode('/', dirname($_SERVER['SCRIPT_NAME']))[1];
        $host = '//' . $_SERVER['HTTP_HOST'];
        return $host . $rootDirectory;
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