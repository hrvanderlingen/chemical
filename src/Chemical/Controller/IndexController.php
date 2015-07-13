<?php

namespace Chemical\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        $viewModel = new ViewModel;
        $viewModel->setTemplate('chemical/index/index.phtml');
        return $viewModel;
    }

}