<?php

namespace Chemical\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Chemical\Service\XMLTreeService;

class ProductController extends AbstractRestfulController
{

    protected $treeService;

    public function __construct(XMLTreeService $treeService)
    {
        $this->treeService = $treeService;
    }

    public function create($data)
    {
        $xml = $this->treeService->getTree($data);
        $response = new \Zend\Http\Response();
        $response->getHeaders()->addHeaderLine('Content-Type', 'text/xml; charset=utf-8');
        $response->setContent($xml);
        return $response;
    }

}
