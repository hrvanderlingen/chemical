<?php

namespace Chemical\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Chemical\Service\TreeService;
use Chemical\Service\JwtService;

class RestController extends AbstractRestfulController
{

    protected $config;
    protected $treeService;
    protected $jwtService;

    public function __construct($config, TreeService $treeService, JwtService $jwtService)
    {
        $this->config = $config;
        $this->treeService = $treeService;
        $this->jwtService = $jwtService;
    }

    public function get($id)
    {

        $headers = $this->getAccessControlHeaders();

        $this->getResponse()->getHeaders()->addHeaders($headers);
        $data = [1, 2, 3, 4];
        return new JsonModel($data);
    }

    public function create($data)
    {
        $headers = $headers = $this->getAccessControlHeaders();

        switch ($this->getRequest()->getRequestUri()) {
            case "/chemistry/rest/new-tree":

                $node = ['node' => ''];
                $data = $this->treeService->getTree($node);
                ini_set('memory_limit', '400MB');
                file_put_contents($this->config['treeStore'] . "/new.xml", $data);
                $response = ['message' => 'tree created'];
                break;

            case "/chemistry/rest/node":

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
            case "/chemistry/rest/service/login":

                $token = $this->jwtService->createToken();
                $response = ['data' => $token->getPayLoad()];
                break;
        }
        $this->getResponse()->getHeaders()->addHeaders($headers);
        return new JsonModel($response);
    }

    public function options()
    {
        $headers = $this->getAccessControlHeaders();
        $this->getResponse()->getHeaders()->addHeaders($headers);
    }

    protected function getAccessControlHeaders()
    {
        return $headers = array(
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'POST,GET',
            'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content-Type, Accept',
        );
    }

}
