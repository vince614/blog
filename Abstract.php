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
}