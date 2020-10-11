<?php

class Users extends Mysql
{

    /**
     * Minimum password length
     */
    const MIN_PASSWORD_LENGTH = 7;

    /**
     * Views types
     */
    const VIEW_TYPE_CHAPTER = 1;

    /**
     * Register account
     * @param $email
     * @param $username
     * @param $password
     * @param $confirmPassword
     * @return string|bool
     */
    public function register($email, $username, $password, $confirmPassword)
    {
        if ($password === $confirmPassword) {
            if (strlen($password) >= self::MIN_PASSWORD_LENGTH) {
                if (!$this->_userExist($email)) {
                    $req = Mysql::_getConnection()->prepare("INSERT INTO users (email, username, password, register_date) VALUES (?, ?, ?, ?)");
                    $req->execute(array($email, $username, sha1($password), time()));
                    return false;
                }
                return "Cette adresse mail existe déjà";
            }
            return "Votre mot de passe dois faire plus de " . self::MIN_PASSWORD_LENGTH - 1 . " caractères";
        }
        return "Veuillez entrez deux mots de passe identique";
    }

    /**
     * Check if user exist
     * @param $email
     * @return bool
     */
    protected function _userExist($email)
    {
        $req = Mysql::_getConnection()->prepare("SELECT * FROM users WHERE email = ?");
        $req->execute(array($email));
        if ($req->rowCount() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Login account
     * @param $email
     * @param $password
     * @return string
     */
    public function login($email, $password)
    {
        if ($this->_userExist($email)) {
            $req = Mysql::_getConnection()->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
            $req->execute(array($email, sha1($password)));
            if ($req->rowCount() > 0) {
                $loginInformation = $req->fetch();
                Mysql::_setIsLogin(
                    [
                        $loginInformation['id'],
                        $loginInformation['email'],
                        $loginInformation['username'],
                        $loginInformation['register_date']
                    ]);

                // Set remember key
                $this->_setRememberKey($loginInformation['id']);

                return false;
            }
            return "Le mot de passe n'est pas correct. Veuillez réessayer";
        }
        return "Aucun compte n'existe avec cette adresse mail";
    }

    /**
     * Check if user is admin
     * @param $userId
     * @return bool
     */
    public function isAdmin($userId)
    {
        $req = Mysql::_getConnection()->prepare("SELECT * FROM users WHERE id = ? AND admin = ?");
        $req->execute(array($userId, 1));
        if ($req->rowCount() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Get user by id
     * @param $userId
     * @return bool
     */
    public function getUserById($userId)
    {
        $req = Mysql::_getConnection()->prepare("SELECT * FROM users WHERE id = ?");
        $req->execute(array($userId));
        return $req->fetch();
    }

    /**
     * Chapter write by user with user ID.
     * @param $userId
     * @return array
     */
    public function getChaptersWriter($userId)
    {
        $req = Mysql::_getConnection()->prepare("SELECT * FROM tickets WHERE author_id = ?");
        $req->execute(array($userId));
        return [
            'count' => $req->rowCount(),
            'chapters' => $req->fetchAll()
        ];
    }

    /**
     * Comments count by userr id
     * @param $userId
     * @return int
     */
    public function getCommentsCount($userId)
    {
        $req = Mysql::_getConnection()->prepare("SELECT * FROM comments WHERE authorId = ?");
        $req->execute(array($userId));
        return $req->rowCount();

    }

    /**
     * Get chapter views count
     * @param $userId
     * @return int
     */
    public function getChapterViewCount($userId)
    {
        $req = Mysql::_getConnection()->prepare("SELECT * FROM views WHERE user_id = ?");
        $req->execute(array($userId));
        return $req->rowCount();
    }


    /**
     * Set remember key
     * @param $userId
     * @return bool
     */
    protected function _setRememberKey($userId)
    {
        // Delete all old keys
        $req = Mysql::_getConnection()->prepare("DELETE FROM remember_me WHERE user_id = ?");
        $req->execute(array($userId));

        while (true) {

            // Generate key
            $key = $this->_generateRememberKey(30);

            // Check if key exist
            $req = Mysql::_getConnection()->prepare("SELECT * FROM remember_me WHERE remember_key = ?");
            $req->execute(array($key));
            if ($req->rowCount() == 0) {

                // Insert key in database
                $req = Mysql::_getConnection()->prepare("INSERT INTO remember_me (user_id, remember_key) VALUES (?, ?)");
                $req->execute(array($userId, $key));

                // Set cookie for 30 days
                $cookieDaysExpire = 30;
                setcookie('remember_key', $key, time() + 3600 * 24 * $cookieDaysExpire, '/');

                // Stop loop
                return true;
            }
        }
    }
}