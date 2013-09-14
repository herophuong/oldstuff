<?php
namespace UserTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use User\Entity\User;

class LoginControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;
    protected $em = null;
    protected $user = null;
    
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
                'email' => 'user@example.com',
                'password' => $bcrypt->create('test'),
                'state' => 1,
            ));
            $this->em->persist($this->user);
            $this->em->flush();
        }
    }
    
    
    
    public function testLoginLayout()
    {
        $this->dispatch('/login');
        
        // At least should has these fields
        $this->assertQuery('input[name="email"]');
        $this->assertQuery('input[name="password"]');
    }
    
    public function testLoginWithValidCredential()
    {
        $data['email'] = 'user@example.com';
        $data['password'] = 'test';
        $this->dispatch('/login', 'POST', $data);
        
        // Make sure the system has identity
        $authService = $this->getApplicationServiceLocator()->get('Zend\Authentication\AuthenticationService');
        $this->assertTrue($authService->getIdentity() !== null);
    }
    
    public function testLoginWithUnsignedEmail()
    {
        $data['email'] = 'abcdef@example.com';
        $data['password'] = 'test';
        $this->dispatch('/login', 'POST', $data);
        
        // Make sure some error messages appear
        $this->assertQuery('div.alert-danger');
    }
    
    public function testLoginWithInvalidEmail()
    {
        $data['email'] = 'abcdef';
        $data['password'] = 'test';
        $this->dispatch('/login', 'POST', $data);
        
        // Make sure some error messages appear
        $this->assertQuery('div.alert-danger');
    }
    
    public function testLoginWithInvalidPassword()
    {
        $data['email'] = 'user@example.com';
        $data['password'] = 'testtest';
        $this->dispatch('/login', 'POST', $data);
        
        // Make sure some error messages appear
        $this->assertQuery('div.alert-danger');
    }
    
    public function testLoginActionCanBeAccessed()
    {
        $this->dispatch('/login');
        $this->assertResponseStatusCode(200);
        
        $this->assertModuleName('User');
        $this->assertControllerName('User\Controller\User');
        $this->assertControllerClass('UserController');
        $this->assertMatchedRouteName('login');
    }
}
    