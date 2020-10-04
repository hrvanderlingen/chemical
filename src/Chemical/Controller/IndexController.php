<?php

namespace Chemical\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Authy\Rest\Client;

class IndexController extends AbstractActionController
{

    protected $config;
    

    public function __construct($config)
    {
        $this->config = $config;      
    }

    public function indexAction()
    {        

        

        // Find your Account Sid and Auth Token at twilio.com/console
        // DANGER! This is insecure. See http://twil.io/secure
        $sid    = "SKf5e29d72ceb519a2114086f8f4fb7ed8";
        $token  = "Z22p8xJ7uL3OBN1m5i8rbwr7CcOCwEai";
        $twilio = new Client($sid, $token);

        $new_key = $twilio->newKeys
                        ->create();

        print($new_key->sid);


        echo $this->config['authy']['api_key'];
        $authy_api = new \Authy\AuthyApi($this->config['authy']['api_key']);
        $user = $authy_api->registerUser('new_user@email.com', '405-342-5699', 57); 

        if($user->ok()) {
            printf($user->id());
        } else {
            foreach($user->errors() as $field => $message) {
                printf("$field = $message\n");
            }
        }
        exit;
        return new ViewModel();
    }
}
