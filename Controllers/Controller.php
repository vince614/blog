<?php

/**
 * Class Controller
 */
class Controller extends Core_Abstract
{

    /**
     * Variables
     * @var $vars array
     */
    public $vars = [];

    /**
     * Display HTML content on the view
     * @param $view
     * @return false|string
     */
    protected function render($view)
    {
        extract($this->vars);

        /**
         * Include nav bar
         */
        require 'Views/blocks/navBar.phtml';

        /**
         * Include template views
         */
        $viewFile = 'Views/' . str_replace('.', '/', $view) . '.phtml';
        if (file_exists($viewFile)) {
            require $viewFile;
        }else {
            $this->notFound();
        }

        /**
         * Include end of file
         */
        require 'Views/blocks/endPage.phtml';
    }

    /**
     * Set variables
     * @param $index
     * @param $value
     * @return mixed
     */
    public function setVar($index, $value)
    {
        return $this->vars[$index] = $value;
    }

    /**
     * Redirect if page not found
     */
    public function notFound()
    {
        header('HTTP/1.0 404 Not Found');
        return die('La page est introuvable');
    }

    /**
     * Redirect if don't have accès
     */
    public function forbidden()
    {
        header('HTTP/1.0 403 Forbidden');
        die('Accès interdit');
    }

    /**
     * Get user id
     * @return int
     */
    public function getUserId() {
        return $_SESSION['user']['id'];
    }

    /**
     * Get user email
     * @return string
     */
    public function getEmail() {
        return $_SESSION['user']['email'];
    }

    /**
     * Get username
     * @return string
     */
    public function getUsername() {
        return ucfirst($_SESSION['user']['username']);
    }

    /**
     * Get user registration date
     * @return int
     */
    public function getRegisterDate() {
        return $_SESSION['user']['register_date'];
    }

    /**
     * Check if user is logged
     * @return bool
     */
    public function isLogin()
    {
        return isset($_SESSION['user']);
    }

    /**
     * Get post requests
     * @return mixed
     */
    public function getPostRequest()
    {
        return $_POST;
    }
}