<?php

class ChaptersController extends Controller
{

    /**
     * Tickets model path
     */
    const TICKETS_MODEL_PATH = 'Models/Tickets.php';

    /**
     * Ticket model path
     */
    const COMMENTS_MODEL_PATH = 'Models/Comments.php';

    /**
     * Ticket model path
     */
    const USERS_MODEL_PATH = 'Models/Users.php';

    /**
     * Model instance
     * @var $_ticketsManager Tickets
     */
    private $_ticketsManager;

    /**
     * Model instance
     * @var $_commentManager Comments
     */
    private $_commentManager;

    /**
     * Model instance
     * @var $_userManager Users
     */
    private $_userManager;

    /**
     * Parametre
     * @var $_param int
     */
    private $_param;

    /**
     * ChaptersController constructor.
     * @param $path
     * @param null $param
     */
    public function __construct($path, $param = null)
    {
        if ($param) {
            $this->_param = (int)$param;
        }
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
        $this->initModels();
        $this->_beforeRender();
        $this->render($path);
    }

    /**
     * Init model connection
     */
    private function initModels()
    {
        require_once self::TICKETS_MODEL_PATH;
        require_once self::COMMENTS_MODEL_PATH;
        require_once self::USERS_MODEL_PATH;
        $this->_ticketsManager = new Tickets();
        $this->_commentManager = new Comments();
        $this->_userManager = new Users();
    }

    /**
     * Before render
     */
    private function _beforeRender()
    {
        // If get ticket ID
        if ($this->_param) {
            // Read request if exist
            if ($request = $this->getPostRequest()) {
                // Check if user is login
                if ($this->isLogin()) {
                    $this->_commentManager->createComment($request['comment'], $this->getUserId(), $request['ticketId']);
                } else {
                    // Callback error message
                    echo 'Vous devez être connecté pour écrire un commentaire, connectez vous <a href="' . $this->getHost() . '/account">ici</a>';
                }
                exit;
            } else {
                // Fetch ticket & comments
                $ticket = $this->_ticketsManager->readTicket($this->_param);
                $comments = $this->_commentManager->fetchComments((int)$this->_param);

                // Get markdown & set author
                $ticket['content'] = $this->_getMarkdown($ticket['content']);

                // Estimate read time
                $word = str_word_count(strip_tags($ticket['content']));
                $m = floor($word / 200);
                $s = floor($word % 200 / (200 / 60));
                $readTime = $m . ' minute' . ($m == 1 ? '' : 's') . ', ' . $s . ' seconde' . ($s == 1 ? '' : 's');
                $this->setVar('readTime', $readTime);

                // Get markdow & author
                $commentsResult = [];
                foreach ($comments as $comment) {
                    $comment['comment'] = $this->_getMarkdown($comment['comment']);
                    $comment['author'] = $this->_userManager->getUserById($comment['authorId']);
                    $commentsResult[] = $comment;
                }

                // If have ticket
                if ($ticket) {
                    // Set ticket var in template
                    $this->setVar('ticket', $ticket);
                    if ($comments) {
                        // Set comments var in template
                        $this->setVar('comments', $commentsResult);
                    }
                } else {
                    $this->notFound();
                }
            }
        } else {
            // Fetch all tickets
            $tickets = $this->_ticketsManager->fetchTickets();
            $this->setVar('tickets', $tickets);
        }
    }

    /**
     * Get markdow with Parsedown
     * @param $content
     * @return string
     */
    private function _getMarkdown($content)
    {
        $Parsedown = new Parsedown();
        $Parsedown->setSafeMode(true);
        $Parsedown->setMarkupEscaped(true);
        return nl2br($Parsedown->text($content));
    }


}