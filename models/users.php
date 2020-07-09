<?php

class users extends mysql {

    /**
     * Minimum password length
     */
    const MIN_PASSWORD_LENGTH = 7;

    /**
     * Register account
     * @param $userInformations
     * @return string
     */
    public function register($userInformations) {
        $email = $userInformations[0];
        $username = $userInformations[1];
        $password = $userInformations[2];
        $confirmPassword = $userInformations[3];

        if ($password === $confirmPassword) {
            if (strlen($password) >= self::MIN_PASSWORD_LENGTH) {
                if (!$this->_userExist($email)) {
                    $req = parent::_getConnection()->prepare("INSERT INTO users (email, username, password, register_date) VALUES (?, ?, ?, ?)");
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
        $req = parent::_getConnection()->prepare("SELECT * FROM users WHERE email = ?");
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
            $req = parent::_getConnection()->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
            $req->execute(array($email, sha1($password)));
            if ($req->rowCount() > 0) {
                $loginInformation = $req->fetch();
                parent::_setIsLogin(
                    true,
                    [
                        $loginInformation['id'],
                        $loginInformation['email'],
                        $loginInformation['username'],
                        $loginInformation['register_date']
                    ]);
                return true;
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
}