<?php
namespace Stuff;

use Zend\Session\SessionManager;
use Zend\Session\Container;

class Module{
	public function getAutoloaderConfig(){
		return array(
			'Zend\Loader\ClassMapAutoloader' => array(
				__DIR__.'/autoload_classmap.php',
			),
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}
	
	public function getConfig(){
		return include __DIR__.'/config/module.config.php';
	}
    
    public function onBootstrap($e)
    {
        $session = $e->getApplication()
                     ->getServiceManager()
                     ->get('Zend\Session\SessionManager');
        $session->start();

//         $container = new Container('stuff');
//         if (!isset($container->init)) {
//              $session->regenerateId(true);
//              $container->init = 1;
//         }
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Zend\Session\SessionManager' => function ($sm) {
                    $config = $sm->get('config');
                    if (isset($config['session'])) {
                        $session = $config['session'];

                        $sessionConfig = null;
                        if (isset($session['config'])) {
                            $class = isset($session['config']['class'])  ? $session['config']['class'] : 'Zend\Session\Config\SessionConfig';
                            $options = isset($session['config']['options']) ? $session['config']['options'] : array();
                            $sessionConfig = new $class();
                            $sessionConfig->setOptions($options);
                        }

                        $sessionStorage = null;
                        if (isset($session['storage'])) {
                            $class = $session['storage'];
                            $sessionStorage = new $class();
                        }

                        $sessionSaveHandler = null;
                        if (isset($session['save_handler'])) {
                            // class should be fetched from service manager since it will require constructor arguments
                            $sessionSaveHandler = $sm->get($session['save_handler']);
                        }

                        $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);

                        if (isset($session['validators'])) {
                            $chain = $sessionManager->getValidatorChain();
                            foreach ($session['validators'] as $validator) {
                                $validator = new $validator();
                                $chain->attach('session.validate', array($validator, 'isValid'));

                            }
                        }
                    } else {
                        $sessionManager = new SessionManager();
                    }
                    Container::setDefaultManager($sessionManager);
                    return $sessionManager;
                },
                'Stuff\Navigation\Head' => function($sm) {
                    $authService = $sm->get('Zend\Authentication\AuthenticationService');
                    $container = new \Zend\Navigation\Navigation();
                    \Zend\Navigation\Page\Mvc::setDefaultRouter($sm->get('router'));
                    $routeMatch = $sm->get('Application')->getMvcEvent()->getRouteMatch();
                    if ($user = $authService->getIdentity()) {
                        $container->addPages(array(
                            array(
                                'label' => 'My Stuff',
                                'route' => 'stuff',
                                'route_match' => $routeMatch,
                                'params' => array(
                                    'id' => $user->user_id,
                                    'action' => 'user',
                                ),
                                'icon' => 'briefcase',
                            ),
                            array(
                                'label' => 'Add Stuff',
                                'route' => 'stuff',
                                'route_match' => $routeMatch,
                                'params' => array(
                                    'action' => 'add',
                                ),
                                'icon' => 'plus-sign',
                            ),
                            array(
                                'label' => 'Log out',
                                'route' => 'logout',
                                'icon' => 'share',
                            ),
                        ));
                    } else {
                        $uri = $sm->get('request')->getRequestUri(); // Get current url
                        $uri = preg_replace('/\?.*/', '', $uri); // Remove the query part
                        $container->addPages(array(
                            array(
                                'label' => 'Log in',
                                'route' => 'login',
                                'route_match' => $routeMatch,
                                'query' => array(
                                    'redirect' => $uri,
                                ),
                                'icon' => 'lock',
                            ),
                            array(
                                'label' => 'Sign up',
                                'route' => 'register',
                                'route_match' => $routeMatch,
                                'icon' => 'user',
                            ),
                        ));
                    }
                    return $container;
                }
            ),
        );
    }
}