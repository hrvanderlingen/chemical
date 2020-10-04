<?php

namespace Chemical\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\Validator\EmailAddress;
use Chemical\Service\TreeService;
use Chemical\Service\JwtService;
use Firebase\JWT\JWT;
use Chemical\Service\RscService;
use Chemical\Service\ExistDbService;
use Chemical\Service\VerifyServiceInterface;
use Chemical\Service\UserServiceInterface;

class RestController extends AbstractRestfulController
{

    protected $config;
    protected $treeService;
    protected $jwtService;
    protected $rscService;
    protected $existDbService;
    protected $verifyService;
    protected $userService;

    public function __construct($config, TreeService $treeService, JwtService $jwtService, RscService $rscService,
        ExistDbService $existDbService, VerifyServiceInterface $verifyService, UserServiceInterface $userService)
    {
        $this->config = $config;
        $this->treeService = $treeService;
        $this->jwtService = $jwtService;
        $this->rscService = $rscService;
        $this->existDbService = $existDbService;
        $this->verifyService = $verifyService;
        $this->userService = $userService;
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

        try {
            $payload = $this->jwtService->extractPayload($authorization);
            $username = $payload->data->username;
        } catch (\Exception $e) {
            $data = array(
                'success' => false,
                'errorMessage' => $e->getMessage(),
            );
            $this->response->setStatusCode(401);
            return new JsonModel($data);
        }

        switch ($id) {
            case "new":
                $node = ['node' => ''];
                ini_set('memory_limit', '500MB');
                $tree = $this->treeService->getTree($node);

                file_put_contents($this->config['treeStore'] . "/new.xml", $tree);
                $message = 'OK';
                return new JsonModel(['message' => $message]);
                break;
            case "products":
                $data = [];
                $result = $this->existDbService
                    ->setCollection('products')
                    ->query('GET', '//collection/products/product//productCode');

                if ($this->existDbService->gethasError()) {
                    $this->response->setStatusCode(500);
                    return new JsonModel(['errorMessage' => $this->existDbService->getErrorMessage()]);
                }

                $xml = simplexml_load_string($result);
                foreach ($xml->productCode as $productCode) {
                    $data[] = [
                        'title' => (string) $productCode,
                    ];
                }
                return new JsonModel($data);
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
                return new JsonModel(['error' => 1, 'errorMessage' => 'page not found']);
                break;
        }
    }

    public function create($data)
    {
        $headers = $headers = $this->jwtService->getAccessControlHeaders();

        switch ($this->getRequest()->getRequestUri()) {
            case "/chemistry/rest/service/login":

                $validated = false;
                $validator = new EmailAddress();

                if ($validator->isValid($data['username'])) {
                    $user = $this->userService->authenticate($data['username'], $data['password']);
                }

                if (!is_null($user)) {

                    if ($this->config['twofactor']['active']) {
                        $user->setTwofactorstatus($this->verifyService->sendToken($user->getPhone()));
                    }

                    $this->jwtService->setUser($user->getArrayCopy());
                    $data = $this->jwtService->setJwtData();

                    $secretKey = $this->config['jwt_secret'];
                    $jwt = JWT::encode($data, $secretKey, 'HS512');

                    $user->setToken($jwt);
                    $response = $user->getArrayCopy();
                    $validated = true;
                    break;
                }

                if (!$validated) {
                    $this->response->setStatusCode(401);
                    $response = ['error' => 1, 'errorMessage' => 'Username or password not valid'];
                }

                break;
            case "/chemistry/rest/service/verify":

                $headers = $request->getHeaders();
                $authorizationHeader = $headers->get('Authorization');
                if ($authorizationHeader) {
                    $authorization = $authorizationHeader->getFieldValue();
                } else {
                    $this->response->setStatusCode(500);
                    return new JsonModel([]);
                }

                try {

                    if (!preg_match("/[0-9]{6}/", $data['smscode'])) {
                        throw new \Exception('SMS code invalid');
                    }

                    $payload = $this->jwtService->extractPayload($authorization);
                } catch (\Exception $e) {
                    $data = array(
                        'success' => false,
                        'errorMessage' => $e->getMessage(),
                    );
                    $this->response->setStatusCode(401);
                    return new JsonModel($data);
                }

                $user = $this->userService->find($payload->data->username);

                $status = $this->verifyService->verify($data['smscode'], $user->getPhone());
                $this->response->setStatusCode(200);
                return new JsonModel(['twofactorstatus' => $status]);
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
