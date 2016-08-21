<?php

namespace Chemical\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Chemical\Service\WikiTreeService;
use Chemical\Service\TreeService;

class treeServiceFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return TreeService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {

	$config = $serviceLocator->get('Config');

	if (!isset($config['treeSupplier'])) {
	    $tree = new TreeService();
	    $tree->hasError(true);
	    $tree->setErrorMessage('Tree supplier not configured');
	    return $tree;
	}

	switch ($config['treeSupplier']) {
	    case "wikipedia":
		return new WikiTreeService();
		break;
	    default:
		$tree = new TreeService();
		$tree->hasError(true);
		$tree->setErrorMessage('Invalid tree supplier');
		return $tree;

		break;
	}
    }

}
