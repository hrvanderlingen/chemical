<?php

namespace Chemical\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Chemical\Service\TreeService;

class TreeController extends AbstractRestfulController
{

    protected $treeService;

    public function __construct(TreeService $treeService)
    {
        $this->treeService = $treeService;
    }

    /**
     * In the REST model POST requests are handled by this create method
     * @param array $data containing the node for example "chemistry"
     * @return JsonModel the complete category data tree derived from the initial node
     */
    public function create($data)
    {
        try {
            return new JsonModel(array(
                "data" => $this->treeService->getTree($data)));
        } catch (\Exception $e) {
            $this->getResponse()->setStatusCode(500);
            return new JsonModel(array(
                'error' => $e->getMessage(),
            ));
        }
    }

}
