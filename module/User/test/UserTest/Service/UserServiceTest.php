<?php
namespace UserTest\Service;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use User\Entity\User;

class UserServiceTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;
    protected $em = null;
    protected $user = null;
    protected $userService = null;
    
    const EMAIL = 'user@example.com';
    const PASSWORD = 'test';
    
    public function setUp()
    {
        $this->setApplicationConfig(\UserTest\Bootstrap::getConfig());
        parent::setUp();
        
        // First setup
        $this->getEntityManager();
        $this->getUser();
    }
    
    public function testInitiliazation()
    {
        $this->assertInstanceOf('User\Service\User', $this->getUserService());
        $this->assertInstanceOf('Zend\ServiceManager\ServiceManager', $this->getUserService()->getServiceManager());
    }
    
    public function testValidRegister()
    {
        $data['email'] = 'user2@example.com';
        $data['password'] = 'test';
        $data['passwordconfirmation'] = 'test';
        
        // Make sure upon successfully register, the service return the created entity 
        $this->assertInstanceOf('User\Entity\User', $this->getUserService()->register($data));
        
        // Make sure the created entity is stored into the database
        $this->assertInstanceOf('User\Entity\User', $this->getEntityManager()->getRepository('User\Entity\User')->findOneBy(array('email' => $data['email'])));
    }
    
    public function testInvalidRegister()
    {
        $data['email'] = 'user2@example.com';
        $data['password'] = 'test';
        $data['passwordconfirmation'] = 'tset';
        
        $this->assertNull($this->getUserService()->register($data));
    }
    
    protected function getEntityManager()
    {
        if ($this->em === null) {
            $this->em = $this->getApplicationServiceLocator()->get('doctrine.entitymanager.orm_default');
            $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
            $classes = array($this->em->getClassMetadata('User\Entity\User'));
            $tool->dropSchema($classes);
            $tool->createSchema($classes);
        }
        
        return $this->em;
    }
    
    protected function getAuthService()
    {
        // Get the authentication service
        if ($this->authService === null) {
            $this->authService = $this->getApplicationServiceLocator()->get('Zend\Authentication\AuthenticationService');
        }
        
        return $this->authService;
    }
    
    protected function getUser()
    {
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
            $this->getEntityManager()->persist($this->user);
            $this->getEntityManager()->flush();
        }
        
        return $this->user;
    }
    
    protected function getUserService()
    {
        if ($this->userService == null) {
            $this->userService = $this->getApplicationServiceLocator()->get('User\Service\User');
        }
        
        return $this->userService;
    }
}