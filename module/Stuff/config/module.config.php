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
						)
					,)
				,)
			,)
		,)
);
