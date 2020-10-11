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
     * Pagination mode
     * @var bool
     */
    private $_paginationMode;

    /**
     * ChaptersController constructor.
     * @param $path
     * @param null $param
     * @param bool $paginationMode
     */
    public function __construct($path, $param = null, $paginationMode = false)
    {
        if ($param) $this->_param = (int)$param;
        if ($paginationMode) $this->_paginationMode = (bool) $paginationMode;
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
        // Set isAdmin variable
        $isAdmin = false;
        if ($this->isLogin()) {
            $isAdmin = $this->_userManager->isAdmin($this->getUserId());
        }
        $this->setVar('isAdmin', $isAdmin);

        // If get ticket ID
        if ($this->_param && !$this->_paginationMode) {
            // Read request if exist
            if ($request = $this->getPostRequest()) {
                // Check if user is login
                if ($this->isLogin()) {
                    // Action type
                    switch ($request['action']) {
                        // Create new comment
                        case 'create':
                            $this->_commentManager->createComment(
                                $request['comment'],
                                $this->getUserId(),
                                $request['ticketId']
                            );
                            break;
                        // Signal comment
                        case 'report':
                            $error = $this->_commentManager->signalComment(
                                $request['commentId'],
                                $this->getUserId()
                            );
                            if ($error) echo $error;
                            break;
                        // Delete comment
                        case 'delete':
                            $this->_commentManager->deleteComment(
                                $request['commentId']
                            );
                            break;
                    }
                } else {
                    // Callback error message
                    echo 'Vous devez être connecté, connectez vous <a href="' . $this->getHost() . '/account">ici</a>';
                }
                exit;
            } else {
                // Fetch ticket & comments
                $ticket = $this->_ticketsManager->readTicket($this->_param);
                $comments = $this->_commentManager->fetchComments((int)$this->_param);

                // Estimate read time
                $word = str_word_count(strip_tags($ticket['content']));
                $m = floor($word / 200);
                $s = floor($word % 200 / (200 / 60));
                $readTime = $m . ' minute' . ($m == 1 ? '' : 's') . ', ' . $s . ' seconde' . ($s == 1 ? '' : 's');
                $this->setVar('readTime', $readTime);

                // Get markdow & author
                $commentsResult = [];

                // If have comments
                if ($comments) {
                    foreach ($comments as $comment) {
                        $comment['author'] = $this->_userManager->getUserById($comment['authorId']);
                        $comment['isAuthor'] = $this->isLogin() ? $this->getUserId() == $comment['authorId'] : false;
                        $commentsResult[] = $comment;
                    }
                }

                // If have ticket
                if ($ticket) {
                    // Set ticket var in template
                    $this->setVar('ticket', $ticket);

                    // Check is user is login
                    if ($this->isLogin()) {
                        // Set view in ticket
                        $this->_ticketsManager->setView($this->getUserId(), $ticket['id']);
                    }

                    if ($comments) {
                        // Set comments var in template
                        $this->setVar('comments', $commentsResult);
                    }
                } else {
                    $this->notFound();
                }
            }
        } elseif ($this->_param && $this->_paginationMode) {
            // Fetch tickets & set page
            $tickets = $this->_ticketsManager->fetchTickets($this->_param);
            $this->setVar('tickets', $tickets);
            $this->setVar('currentPage', $this->_param);
        } else {
            // Fetch all tickets
            $tickets = $this->_ticketsManager->fetchTickets();
            $this->setVar('tickets', $tickets);
        }
    }
}