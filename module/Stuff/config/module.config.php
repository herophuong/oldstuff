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
						'stuff_id' => '[0-9]+',
					),
					'defaults' => array(
						'controller' => 'Stuff\Controller\Stuff',
						'action' => 'home',
					),
				),
			),
		),
	),
	'view_manager' => array(
		'template_path_stack' => array(
			'stuff'=> __DIR__.'/../view',
		),
	),
	'view_helpers' => array(
        'invokables' => array(
            'alertBlock' => 'Stuff\View\Helper\AlertBlock',
            'deleteConfirmationScript' => 'Stuff\View\Helper\DeleteConfirmationScript',
            'stuffAddLink' => 'Stuff\View\Helper\StuffAddLink',
            'stuffEditLink' => 'Stuff\View\Helper\StuffEditLink',
            'stuffDeleteLink' => 'Stuff\View\Helper\StuffDeleteLink',
            'stuffUserLink' => 'Stuff\View\Helper\StuffUserLink',
            'stuffItemLink' => 'Stuff\View\Helper\StuffItemLink',
        ),
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
    'session' => array(
        'config' => array(
            'class' => 'Zend\Session\Config\SessionConfig',
            'options' => array(
                'name' => 'stuff',
            ),
        ),
        'storage' => 'Zend\Session\Storage\SessionArrayStorage',
        'validators' => array(
            'Zend\Session\Validator\RemoteAddr',
            'Zend\Session\Validator\HttpUserAgent',
        ),
    ),
);
