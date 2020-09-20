<?php

class ChapterspageController extends ChaptersController
{
    public function __construct($path)
    {
        echo $path;
        return parent::__construct($path);
    }
}