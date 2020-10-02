<?php

class Users extends Mysql {

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
    public function register($email, $username, $password, $confirmPassword) {
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
    protected function _userExist($email) {
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
    public function login($email, $password) {
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
                return false;
            }
            return "Le mot de passe n'est pas correct. Veuillez réessayer";
        }
        return "Aucun compte n'existe avec cette adresse mail";
    }

    /**
     * Logout from sesssion
     */
    public function logout() {
        if (isset($_SESSION['user'])) {
            // Unset user session
            unset($_SESSION['user']);
        }
    }

    /**
     * Check if user is admin
     * @param $userId
     * @return bool
     */
    public function isAdmin($userId) {
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
     * Count chapter write by user with user ID.
     * @param $userId
     * @return int
     */
    public function getChaptersWriterCount($userId)
    {
        $req = Mysql::_getConnection()->prepare("SELECT * FROM tickets WHERE author_id = ?");
        $req->execute(array($userId));
        return $req->rowCount();
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
     * @TODO
     * Faire un système d'édition des chapitres
     * Bien régler l'intégration de l'éditeur de texte externe
     * Règler le message d'erreur lors de la connexion
     * Prévoir un cookie "se souvenir de moi" avec un token de clé crypté
     * Edition / supppresion des commentaires
     */
}