<?php

/**
 * Class AccountController
 */
class AccountController extends Controller
{
    /**
     * Model instance
     * @var $_accountManager
     */
    private $_accountManager;

    /**
     * AccountController constructor.
     * @param $path
     */
    public function __construct($path)
    {
        $this->index($path);
    }

    /**
     * Index
     * @param $path
     */
    public function index($path) {
        if (!isset($path)) {
            $this->notFound();
            return;
        }
        $this->initModel();
        $this->_beforeRender();
        $this->render($path);
    }

    /**
     * Init model connection
     */
    private function initModel() {
        require_once 'Models/Users.php';
        $this->_accountManager = new Users();
    }

    /**
     * Execute before rendering
     */
    private function _beforeRender() {
        // CODE HERE
    }
}