<?php

namespace Chemical\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Chemical\Service\TreeService;
use Chemical\Service\JwtService;
use Firebase\JWT\JWT;

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

        // check JWT token
        $request = $this->getRequest();
        $headers = $request->getHeaders();
        $authorizationHeader = $headers->get('Authorization');
        if ($authorizationHeader) {
            $authorization = $authorizationHeader->getFieldValue();
        } else {
            $this->response->setStatusCode(401);
            return new JsonModel(["error" => 1]);
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

        $headers = $this->jwtService->getAccessControlHeaders();

        $this->getResponse()->getHeaders()->addHeaders($headers);
        $data = [];
        $data[] = ['id' => 1, 'title' => 'a'];
        $data[] = ['id' => 2, 'title' => 'b'];
        $data[] = ['id' => 3, 'title' => 'c'];
        return new JsonModel($data);
    }

    public function create($data)
    {
        $headers = $headers = $this->jwtService->getAccessControlHeaders();

        switch ($this->getRequest()->getRequestUri()) {
            case "/chemistry/rest/new-tree":
                $node = ['node' => ''];
                $tree = $this->treeService->getTree($node);
                ini_set('memory_limit', '400MB');
                file_put_contents($this->config['treeStore'] . "/new.xml", $tree);
                $response = ['message' => 'tree created'];
                break;

            case "/chemistry/rest/tree/node":
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
                $mockCredentials = [
                    [
                        'email' => 'test@example.com',
                        'firstname' => 'Peter',
                        'lastname' => 'Smith',
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
                            'lastname' => $credential['lastname']
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
