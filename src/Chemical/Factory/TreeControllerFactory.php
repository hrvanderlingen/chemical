<?php

namespace Chemical\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Chemical\Controller\TreeController;

class TreeControllerFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
	$sl = $serviceLocator->getServiceLocator();
	$treeService = $sl->get('treeService');

	return new TreeController($treeService);
    }

}
