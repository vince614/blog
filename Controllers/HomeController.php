<?php

class HomeController extends Controller
{

    /**
     * HomeController constructor.
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
        $this->render($path);
    }

}