<?php

class ChaptersController extends Controller
{

    /**
     * Tickets model path
     */
    const TICKETS_MODEL_PATH = 'Models/Tickets.php';

    /**
     * Model instance
     * @var $_ticketsManager
     */
    private $_ticketsManager;

    /**
     * ChaptersController constructor.
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
        require_once self::TICKETS_MODEL_PATH;
        $this->_ticketsManager = new Tickets();
    }

    /**
     * Before render
     */
    private function _beforeRender() {

    }

}