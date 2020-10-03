<?php

namespace Chemical\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{

    protected $config;
    

    public function __construct($config)
    {
        $this->config = $config;      
    }

    public function indexAction()
    {        
        $authy_api = new Authy\AuthyApi($this->config['authy']['api_key']);
        return new ViewModel();
    }
}
