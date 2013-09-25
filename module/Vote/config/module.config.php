<?php
namespace Vote;
return array(
    'controllers' => array(
        'invokables' => array(
            'Vote\Controller\Vote' => 'Vote\Controller\VoteController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'vote' => array(
                'type'    => 'segment',
                'options' => array(
                    'route' => '/vote[/][:user_id][/:action][/:voted_user_id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'user_id' => '[0-9]+',
                        'voted_user_id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Vote\Controller\Vote',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'vote' => __DIR__ . '/../view',
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
);