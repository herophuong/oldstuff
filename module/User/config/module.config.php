<?php
namespace User;
return array(
    'controllers' => array(
        'invokables' => array(
            'User\Controller\User' => 'User\Controller\UserController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'user' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/user[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'User\Controller\User',
                        'action'     => 'index',
                    ),
                ),
            ),
            'register' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/register',
                    'defaults' => array(
                        'controller' => 'User\Controller\User',
                        'action'     => 'register',
                    ),
                ),
            ),
            'login' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/login',
                    'defaults' => array(
                        'controller' => 'User\Controller\User',
                        'action'     => 'login',
                    ),
                ),
            ),
            'logout' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/logout',
                    'defaults' => array(
                        'controller' => 'User\Controller\User',
                        'action'     => 'logout',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'user' => __DIR__.'/../view',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'formField' => 'User\View\Helper\FormField',
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
        ),
        'authentication' => array(
            'orm_default' => array(
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'identity_class' => 'User\Entity\User',
                'identity_property' => 'email',
                'credential_property' => 'password',
                'credential_callable' => function(\User\Entity\User $user, $passwordGiven) {
                    $bcrypt = new \Zend\Crypt\Password\Bcrypt();
                    return $bcrypt->verify($passwordGiven, $user->password);
                },
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Authentication\AuthenticationService' => function($serviceManager) {
                return $serviceManager->get('doctrine.authenticationservice.orm_default');
            },
            'anonymous_user_navigation' => 'User\Navigation\Service\AnonymousUserNavigationFactory',
            'signedin_user_navigation' => 'User\Navigation\Service\SignedInUserNavigationFactory',
        ),
    ),
    'navigation' => array(
        'anonymous_user' => array(
            array(
                'label' => 'Log in',
                'route' => 'login',
            ),
            array(
                'label' => 'Sign up',
                'route' => 'register',
            ),
        ),
        'signedin_user' => array(
            array(
                'label' => 'Profile',
                'route' => 'user',
                'action' => 'profile',
            ),
            array(
                'label' => 'Logout',
                'route' => 'logout',
            ),
        ),
    ),
);