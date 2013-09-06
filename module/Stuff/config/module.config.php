<?php
namespace Stuff;
return array(
	'controllers' => array(
		'invokables' => array(
			'Stuff\Controller\Stuff' => 'Stuff\Controller\StuffController',
		),
	),
	'router' => array(
		'routes' => array(
			'stuff' => array(
				'type' => 'segment',
				'options' => array(
					'route' => '/stuff[/][:action][/:id]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id' => '[0-9]+',
					),
					'defaults' => array(
						'controllers' => 'Stuff\Controller\Stuff',
						'action' => 'index',
					),
				),
			),
		),
	),
	'view_manager' => array(
		'template_path_stack' => array(
			'stuff'=> __DIR__.'/../view',
		)
	),
	'doctrine' => array(
    	'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
    	)
	),
);
