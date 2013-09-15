<?php
namespace UserTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use User\Entity\User;

abstract class AbstractUserControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;
    protected $em = null;
    protected $user = null;
    protected $authService = null;
    
    const EMAIL = 'user@example.com';
    const PASSWORD = 'test';
    
    public function setUp()
    {
        $this->setApplicationConfig(\UserTest\Bootstrap::getConfig());
        parent::setUp();
        
        // Load the entity manager once
        if ($this->em == null) {
            $this->em = $this->getApplicationServiceLocator()->get('doctrine.entitymanager.orm_default');
            $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
            $classes = array($this->em->getClassMetadata('User\Entity\User'));
            $tool->dropSchema($classes);
            $tool->createSchema($classes);
        }
        
        // Create a new user once
        if ($this->user == null) {
            $this->user = new User();
            $bcrypt = new \Zend\Crypt\Password\Bcrypt();
            $bcrypt->setCost(4); // lower cost for faster test
            $this->user->populate(array(
                'user_id' => 1,
                'display_name' => 'User',
                'email' => self::EMAIL,
                'password' => $bcrypt->create(self::PASSWORD),
                'state' => 1,
            ));
            $this->em->persist($this->user);
            $this->em->flush();
        }
        
        // Get the authentication service
        if ($this->authService == null) {
            $this->authService = $this->getApplicationServiceLocator()->get('Zend\Authentication\AuthenticationService');
        }
    }
    
}