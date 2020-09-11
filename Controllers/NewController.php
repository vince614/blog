<?php

class NewController extends Controller
{

    /**
     * Ticket model path
     */
    const TICKETS_MODEL_PATH = 'Models/Tickets.php';

    /**
     * Model instance
     * @var Tickets $_ticketsManager
     */
    private $_ticketsManager;

    /**
     * NewController constructor.
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
    public function index($path)
    {
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
    private function initModel()
    {
        require_once self::TICKETS_MODEL_PATH;
        $this->_ticketsManager = new Tickets();
    }

    /**
     * Execute before rendering
     */
    private function _beforeRender()
    {
        $request = $this->getPostRequest();
        if ($request) {
            $this->_ticketsManager->createTicket(
                $request['chapterTitle'],
                $request['chapterNumber'],
                $request['chapterContent'],
                $request['author']
            );
            exit;
        }
    }

}