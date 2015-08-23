<?php

namespace Chemical\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Chemical\Service\TemperatureService;

class temperatureServiceFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TemperatureService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {		
	return new TemperatureService();
    }

}
