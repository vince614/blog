<?php

class NewController extends Controller
{

    /**
     * Models path
     */
    const TICKETS_MODEL_PATH = 'Models/Tickets.php';
    const USERS_MODEL_PATH = 'Models/Users.php';

    /**
     * Model instance
     * @var Tickets $_ticketsManager
     */
    private $_ticketsManager;

    /**
     * Model instance
     * @var Users $_usersManager
     */
    private $_usersManager;

    /**
     * Chapter ID
     * @var int $_idChapter
     */
    private $_idChapter;

    /**
     * NewController constructor.
     * @param $path
     * @param null $chapterId
     */
    public function __construct($path, $chapterId = null)
    {
        // Edit chapter
        if ($chapterId) {
            $this->_idChapter = (int)$chapterId;
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
        require_once self::USERS_MODEL_PATH;
        $this->_ticketsManager = new Tickets();
        $this->_usersManager = new Users();
    }

    /**
     * Execute before rendering
     */
    private function _beforeRender()
    {
        if ($this->_idChapter) {
            $ticket = $this->_ticketsManager->readTicket($this->_idChapter);
            $ticket ? $this->setVar('ticket', $ticket) : $this->notFound();
        }
        $request = $this->getPostRequest();
        if ($request) {

            // Is user isn't admin return Permission denied
            if (!$this->_usersManager->isAdmin($this->getUserId())) {
                exit('Autorisation refusés');
            }

            // Delete chapter
            if (isset($request['type']) && $request['type'] === "delete") {
                $this->_ticketsManager->deleteTicket($request['ticketId']);
            }

            // Edit mode
            if (isset($request['editMode']) && $request['editMode'] == 1) {
                if (isset($_FILES["chapterIllustration"])) {
                    $error = $this->_checkAndUploadFile($_FILES, $request['chapterNumber']);
                    if ($error) {
                        echo $error;
                    }
                }
                $this->_ticketsManager->editTicket(
                    $request['chapterTitle'],
                    $request['chapterNumber'],
                    $request['chapterContent'],
                    $this->getUserId(),
                    $request['ticketId']
                );
            } else {
                if (isset($request['chapterNumber'], $request['chapterTitle'], $request['chapterContent'])) {
                    $error = $this->_checkAndUploadFile($_FILES, $request['chapterNumber']);
                    if ($error) {
                        echo $error;
                    } else {
                        $error = $this->_ticketsManager->createTicket(
                            $request['chapterTitle'],
                            $request['chapterNumber'],
                            $request['chapterContent'],
                            $this->getUserId()
                        );
                        if ($error) {
                            echo $error;
                        }
                    }
                }
            }
            exit;
        }
    }

    /**
     * Check & upload file
     * @param $file
     * @param $chapterNumber
     * @return string
     */
    protected function _checkAndUploadFile($file, $chapterNumber)
    {
        if (file_exists('uploads')) {

            // Allowed formats & types
            $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");

            // File info
            $fileName = $file["chapterIllustration"]["name"];
            $fileTmpName = $file["chapterIllustration"]["tmp_name"];
            $fileType = $file["chapterIllustration"]["type"];
            $fileSize = $file["chapterIllustration"]["size"];

            // Check extension of file
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            if (!array_key_exists($ext, $allowed)) {
                return 'Veuillez sélectionner un format de fichier valide.';
            }

            // Check size of file
            $maxsize = 5 * 1024 * 1024;
            if ($fileSize > $maxsize) {
                return 'La taille du fichier est supérieure à la limite autorisée.';
            }

            // Check MIME type of file
            if (in_array($fileType, $allowed)) {
                // Upload file
                move_uploaded_file($fileTmpName, "uploads/" . 'chapter_' . $chapterNumber . '.png');
            } else {
                return "Il y a eu un problème de téléchargement de votre fichier. Veuillez réessayer.";
            }
        }
    }

}