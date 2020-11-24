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

        
        $sid = $this->config['twilio']['account_sid'];
        $token = $this->config['twilio']['auth_token'];
        ;
        $twilio = new Client($sid, $token);

        $new_key = $twilio->newKeys
                        ->create();
     
        echo $this->config['authy']['api_key'];
        $authy_api = new \Authy\AuthyApi($this->config['authy']['api_key']);
        $user = $authy_api->registerUser('new_user@email.com', '405-342-5699', 57); 

        if($user->ok()) {
            
        } else {
            foreach($user->errors() as $field => $message) {
                printf("$field = $message\n");
            }
        }
       
        return new ViewModel();
    }
}
