<?php

namespace Chemical\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        $viewModel = new ViewModel;

        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        
        /**
         * File served from module public_html folder
         * http://ocramius.github.io/blog/asset-manager-for-zend-framework-2/
         */
     
        $renderer->headScript()->appendFile('/js/periodic.js');
        $renderer->headLink()->appendStylesheet('/css/periodic.css');
        $viewModel->setTemplate('chemical/index/index.phtml');

        return $viewModel;
    }

}
