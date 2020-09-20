<?php

/**
 * Class Core_Abstract
 */
class Core_Abstract
{
    /**
     * Get url with GET_METHOD
     * @return string
     */
    public function getUrl()
    {
        if (isset($_GET['url'])) {
            return $_GET['url'];
        }
        return '/';
    }

    /**
     * Get host
     * @return string
     */
    public function getHost()
    {
        $rootDirectory = '/' . explode('/', dirname($_SERVER['SCRIPT_NAME']))[1];
        $host = '//' . $_SERVER['HTTP_HOST'];
        if ($host == '//localhost' OR $host == '//127.0.0.1') {
            $host = $rootDirectory;
        }
        return $host;
    }
}