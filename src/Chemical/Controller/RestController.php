<?php

namespace Chemical\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class RestController extends AbstractRestfulController
{

    public function get($id)
    {
	$headers = array(
	    'Access-Control-Allow-Origin' => '*'
	);

	$this->getResponse()->getHeaders()->addHeaders($headers);
	return new JsonModel(array("data" => array(array('scale' => 'Kelvin', 'value' => $id), array('scale' => 'Celsius', 'value' => ($id - 273.15)))));
    }

    public function create($data)
    {
	$headers = array(
	    'Access-Control-Allow-Origin' => '*'
	);

	$this->getResponse()->getHeaders()->addHeaders($headers);
	return new JsonModel(array("data" => array(array('scale' => 'Kelvin', 'value' => 500), array('scale' => 'Celsius', 'value' => (500 - 273.15)))));
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
