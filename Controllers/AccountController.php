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
        var_dump($request);
        if ($request) {
            $data = [
                $request['email'],
                $request['username'],
                $request['password'],
                $request['confirmPassword']
            ];
            $this->_accountManager->register($data) ? $this->errors[] = $this->_accountManager->register($data) : true;
        }
    }

    /**
     * Return request
     * @return mixed
     */
    private function getPostRequest() {
        return $_POST;
    }
}