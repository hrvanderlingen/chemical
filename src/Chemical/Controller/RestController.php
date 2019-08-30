<?php

namespace Chemical\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Chemical\Service\TreeService;

class RestController extends AbstractRestfulController
{

    protected $config;
    protected $treeService;

    public function __construct($config, TreeService $treeService)
    {
        $this->config = $config;
        $this->treeService = $treeService;
    }

    public function get($id)
    {

        $headers = array(
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET',
        );

        $this->getResponse()->getHeaders()->addHeaders($headers);
        $data = [1, 2, 3, 4];
        return new JsonModel($data);
    }

    public function create($data)
    {
        $headers = array(
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'POST',
        );

        switch ($data['action']) {
            case "newTree":

                $node = ['node' => ''];
                $data = $this->treeService->getTree($node);
                ini_set('memory_limit', '400MB');
                file_put_contents($this->config['treeStore'] . "/new.xml", $data);
                $response = ['message' => 'tree created'];
                break;

            case "node":

                ini_set('memory_limit', '400MB');

                $path = $this->config['treeStore'] . '/default.xml';

                $xml = simplexml_load_file($path);
                $response = [];
                if ($xml instanceof \SimpleXMLElement) {

                    $dom = new \DOMDocument('1.0', 'utf-8');
                    $dom->preserveWhiteSpace = false;
                    $dom->load($path);
                    $xPath = new \DOMXPath($dom);
                    $product = $xPath->query('(//product/productCode)[1]');

                    $response = ['data' => $product->item(0)->nodeValue];
                }
                break;
        }
        $this->getResponse()->getHeaders()->addHeaders($headers);
        return new JsonModel($response);
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
