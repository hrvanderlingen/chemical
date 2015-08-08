<?php

return array(
    'view_manager' => array(
        'template_path_stack' => array(
            'chemistry' => __DIR__ . '/../view',
        ),
        'template_map' => array(
            'chemical/layout' => __DIR__ . '/../view/layout/layout.phtml',
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
                        'action' => 'index',
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
    'asset_manager' => array(
        'resolver_configs' => array(
            'prioritized_paths' => array(
                array(
                    "path" => __DIR__ . '/../public_html',
                    "priority" => 100
                ),
                array(
                    "path" => __DIR__ . '/../../../diniska/chemistry/PeriodicalTable',
                    "priority" => 50
                )
            ),
        ),
    ),
);
