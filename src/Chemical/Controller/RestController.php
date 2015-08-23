<?php

namespace Chemical\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Chemical\Service\TemperatureService;
use Chemical\Service\TemperatureServiceInterface;

class RestController extends AbstractRestfulController
{

    protected $temperatureService;

    public function __construct(TemperatureServiceInterface $temperatureService)
    {
	$this->temperatureService = $temperatureService;
    }

    public function get($id)
    {
	$headers = array(
	    'Access-Control-Allow-Origin' => '*'
	);
	$kelvinScale = $this->params()->fromRoute('id');
	$this->getResponse()->getHeaders()->addHeaders($headers);
	return new JsonModel(array(
	    "data" => $this->temperatureService->getTemperatureConversionArray($kelvinScale)));
    }

    public function create($data)
    {
	$headers = array(
	    'Access-Control-Allow-Origin' => '*'
	);
	$kelvinScale = $this->params()->fromRoute('id');
	$this->getResponse()->getHeaders()->addHeaders($headers);
	return new JsonModel(array(
	    "data" => $this->temperatureService->getTemperatureConversionArray($kelvinScale)));
    }

    public function options()
    {
	$headers = array(
	    'Access-Control-Allow-Origin' => '*',
	    'Access-Control-Allow-Methods' => 'GET, POST, PUT',
	    'Access-Control-Allow-Headers' => 'content-type'
	);
	$this->getResponse()->getHeaders()->addHeaders($headers);
    }

}
