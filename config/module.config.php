<?php

return array(
    'view_manager' => array(
        'template_path_stack' => array(
            'chemistry' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'chemical/layout' => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    'router' => array(
        'routes' => array(
            'chemistry' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/chemistry',
                    'defaults' => array(
                        'controller' => 'Chemical\Controller\IndexController',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'trees' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/trees[/:sub]',
                            'defaults' => array(
                                'controller' => 'Chemical\Controller\IndexController',
                                'action' => 'index'
                            ),
                        ),
                        'may_terminate' => true,
                    ),
                    'rest' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/rest',
                            'defaults' => array(
                                'controller' => 'Rest',
                                'action' => ''
                            ),
                        ),
                    ),
                    'rest-tree' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/rest/tree/:id',
                            'defaults' => array(
                                'controller' => 'Rest',
                                'action' => ''
                            ),
//                            'constraints' => array(
//                                'id' => '[a-z]{1,11}'
//                            )
                        ),
                    ),
                    'service-tree' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/rest/service/:id',
                            'defaults' => array(
                                'controller' => 'Rest',
                                'action' => ''
                            ),
                        ),
                    ),
                    'tree' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/tree[/:action]',
                            'defaults' => array(
                                'controller' => 'Chemical\Controller\IndexController',
                                'action' => 'tree'
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'treeService' => function($container) {
                return new Chemical\Service\XMLTreeService( );
            },
            'jwtService' => function($container) {
                return new Chemical\Service\JwtService($container->get('config'));
            },
        )
    ),
    'controllers' => array(
        'invokables' => array(),
        'factories' => array(
            Rest::class => function($container) {
                return new Chemical\Controller\RestController(
                    $container->get('config'), $container->get('treeService'), $container->get('jwtService')
                );
            },
            Chemical\Controller\IndexController::class => function($container) {
                return new Chemical\Controller\IndexController();
            }
        )
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'prioritized_paths' => array(
                array(
                    "path" => __DIR__ . '/../public_html',
                    "priority" => 100
                ),
            ),
        ),
    ),
);
