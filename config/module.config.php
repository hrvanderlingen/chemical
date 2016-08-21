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
		    'rest' => array(
			'type' => 'segment',
			'options' => array(
			    'route' => '/rest/kelvin[/:id]',
			    'defaults' => array(
				'controller' => 'rest',
				'action' => ''

				),
			    'constraints' => array(
				'id' => '\d{0,4}'
			    )
			),
		    ),
		    'tree' => array(
			'type' => 'segment',
			'options' => array(
			    'route' => '/tree',
			    'defaults' => array(
				'controller' => 'Tree',
				'action' => ''
			    ),
			),
		    ),
		    'trees' => array(
			'type' => 'segment',
			'options' => array(
			    'route' => '/trees',
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
	    'temperatureService' => 'Chemical\Factory\TemperatureServiceFactory',
	    'treeService' => 'Chemical\Factory\TreeServiceFactory'
	)
    ),
    'controllers' => array(
	'invokables' => array(
	    'Chemical\Controller\IndexController'
	    => 'Chemical\Controller\IndexController'
	    
	),
	'factories' => array(
	    'Rest' => 'Chemical\Factory\RestControllerFactory',
	    'Tree' => 'Chemical\Factory\TreeControllerFactory'
	)
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
