<?php

namespace Chemical\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Chemical\Service\XMLTreeService;

class XMLTreeServiceFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return XMLTreeService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new XMLTreeService();
    }

}
