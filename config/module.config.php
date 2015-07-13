<?php

return array(
    'view_manager' => array(
        'template_path_stack' => array(
            'chemistry' => __DIR__ . '/../view',
        ),
    ),
   
    'router' => array(
        'routes' => array(
            'chemistry' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/chemistry',
                    'defaults' => array(
                        'controller' => 'chemistry',
                        'action'     => 'index',
                    ),
                ),               
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Chemistry'
            => 'Chemical\Controller\IndexController'
        ),
    ),
);
