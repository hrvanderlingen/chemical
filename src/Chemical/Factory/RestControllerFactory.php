<?php

namespace Chemical\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Chemical\Controller\RestController;

class RestControllerFactory implements FactoryInterface
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
	$temperatureService = $sl->get('temperatureService');

	return new RestController($temperatureService);
    }

}
