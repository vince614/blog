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
            $error = $this->_checkAndUploadFile($_FILES, $request['chapterNumber']);
            if ($error) {
                echo $error;
            }else {
                $this->_ticketsManager->createTicket(
                    $request['chapterTitle'],
                    $request['chapterNumber'],
                    $request['chapterContent'],
                    $this->getUsername()
                );
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