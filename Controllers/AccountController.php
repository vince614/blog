<?php

/**
 * Class AccountController
 */
class AccountController extends Controller
{

    /**
     * User model path
     */
    const USER_MODEL_PATH = 'Models/Users.php';

    /**
     * Model instance
     * @var $_accountManager
     */
    private $_accountManager;

    /**
     * Callback message
     * @var array
     */
    private $results = [];

    /**
     * Errors
     * @var array
     */
    public $errors = [];

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
        require_once self::USER_MODEL_PATH;
        $this->_accountManager = new Users();
    }

    /**
     * Execute before rendering
     */
    private function _beforeRender() {
        $request = $this->getPostRequest();
        if ($request) {
            /**
             * Get request type
             * If is login or register
             */
            if ($request['type'] === "login") {
                $result = $this->_accountManager->login(
                    $request['email'],
                    $request['password']
                );
            }else {
                $result = $this->_accountManager->register(
                    $request['email'],
                    $request['username'],
                    $request['password'],
                    $request['confirmPassword']
                );
            }
            /**
             * If error message
             */
            if ($result) {
                echo $result;
            }
            exit;
        }
    }

    /**
     * Check if user is admin
     * @return bool
     */
    public function isAdmin() {
        $userId = $this->getUserId();
        return $this->_accountManager->isAdmin($userId);
    }
}