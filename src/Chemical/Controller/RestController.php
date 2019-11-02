<?php

namespace Chemical\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Chemical\Service\TreeService;
use Chemical\Service\JwtService;
use Firebase\JWT\JWT;
use Chemical\Service\RscService;

class RestController extends AbstractRestfulController
{

    protected $config;
    protected $treeService;
    protected $jwtService;
    protected $rscService;

    public function __construct($config, TreeService $treeService, JwtService $jwtService, RscService $rscService)
    {
        $this->config = $config;
        $this->treeService = $treeService;
        $this->jwtService = $jwtService;
        $this->rscService = $rscService;
    }

    public function get($id)
    {
        // check JWT token
        $headers = $this->jwtService->getAccessControlHeaders();
        $this->getResponse()->getHeaders()->addHeaders($headers);
        $request = $this->getRequest();
        $headers = $request->getHeaders();
        $authorizationHeader = $headers->get('Authorization');
        if ($authorizationHeader) {
            $authorization = $authorizationHeader->getFieldValue();
        } else {
            $this->response->setStatusCode(200);
            return new JsonModel([]);
        }

        list($jwt_token) = sscanf($authorization, 'Bearer %s');

        try {
            $secretKey = $this->config['jwt_secret'];
            $payload = JWT::decode($jwt_token, $secretKey, array('HS512'));
            $username = $payload->data->username;
        } catch (\Exception $e) {
            $data = array(
                'success' => false,
                'message' => $e->getMessage(),
            );
            $this->response->setStatusCode(400);
            return new JsonModel($data);
        }

        switch ($id) {
            case "new":
                $node = ['node' => ''];
                $tree = $this->treeService->getTree($node);
                ini_set('memory_limit', '400MB');
                file_put_contents($this->config['treeStore'] . "/new.xml", $tree);
                $message = 'OK';
                return new JsonModel(['message' => $message]);
                break;
            case "node":
                $path = $this->config['treeStore'] . '/new.xml';
                $xml = simplexml_load_file($path);
                $data = [];

                $message = 'OK';
                if ($xml instanceof \SimpleXMLElement) {
                    $dom = new \DOMDocument('1.0', 'utf-8');
                    $dom->preserveWhiteSpace = false;
                    $dom->load($path);
                    $xPath = new \DOMXPath($dom);
                    $products = $xPath->query("//collection/products/product");

                    foreach ($products as $product) {
                        $data[] = [
                            'title' => $product->getElementsByTagName('productCode')[0]->nodeValue,
                        ];
                    }
                }

                return new JsonModel($data);
                break;
            case "rsc-data-resources":
                $data = $this->rscService->getSources();
                $this->response->setStatusCode($data['code']);
                return new JsonModel($data['body']);
                break;
            default:
                $this->response->setStatusCode(404);
                return new JsonModel(['error' => 1, 'reason' => 'page not found']);
                break;
        }
    }

    public function create($data)
    {
        $headers = $headers = $this->jwtService->getAccessControlHeaders();

        switch ($this->getRequest()->getRequestUri()) {
            case "/chemistry/rest/service/login":
                $mockCredentials = [
                    [
                        'email' => 'test@example.com',
                        'firstname' => 'Peter',
                        'lastname' => 'Smith',
                        'role' => 'Admin',
                        'hash' => password_hash('notsosecret', PASSWORD_DEFAULT)
                    ]
                ];
                $validated = false;

                foreach ($mockCredentials as $credential) {
                    if ($data['username'] === $credential['email'] &&
                        password_verify($data['password'], $credential['hash'])) {
                        $user = [
                            'username' => $data['username'],
                            'firstname' => $credential['firstname'],
                            'lastname' => $credential['lastname'],
                            'role' => $credential['role']
                        ];

                        $this->jwtService->setUser($user);
                        $data = $this->jwtService->setJwtData();

                        $secretKey = $this->config['jwt_secret'];
                        $jwt = JWT::encode($data, $secretKey, 'HS512');

                        $user['token'] = $jwt;
                        $response = $user;
                        $validated = true;
                        break;
                    }
                }

                if (!$validated) {
                    $this->response->setStatusCode(401);
                    $response = ['error' => 1, 'errorMessage' => 'Username or password not valid'];
                }

                break;
        }
        $this->getResponse()->getHeaders()->addHeaders($headers);
        return new JsonModel($response);
    }

    public function options()
    {
        $headers = $this->jwtService->getAccessControlHeaders();
        $this->getResponse()->getHeaders()->addHeaders($headers);
    }
}
