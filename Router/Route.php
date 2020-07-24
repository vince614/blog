<?php

/**
 * Class Route
 */
class Route
{

    /**
     * Path
     * @var string
     */
    private $path;

    /**
     * Callable
     * @var mixed
     */
    private $callable;

    /**
     * Matches
     * @var array
     */
    private $matches = [];

    /**
     * Params
     * @var array
     */
    private $params = [];

    /**
     * Route constructor.
     * @param $path
     * @param $callable
     */
    public function __construct($path, $callable)
    {
        $this->path = trim($path, '/');  // On retire les / inutils
        $this->callable = $callable;
    }

    /**
     * Permettra de capturer l'url avec les paramètre
     * get('/posts/:slug-:id') par exemple
     * @param $url
     * @return bool
     */
    public function match($url)
    {
        $url = trim($url, '/');
        $path = preg_replace('#:([\w]+)#', '([^/]+)', $this->path);
        $regex = "#^$path$#i";
        if (!preg_match($regex, $url, $matches)) {
            return false;
        }
        array_shift($matches);
        $this->matches = $matches;
        return true;
    }

    /**
     * Call route
     * @return mixed
     */
    public function call()
    {
        return call_user_func_array($this->callable, $this->matches);
    }
}