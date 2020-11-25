<?php

namespace Chemical\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Authy\Rest\Client;

class IndexController extends AbstractActionController
{

    protected $config;
    

    public function __construct($config)
    {
        $this->config = $config;      
    }

   
}
