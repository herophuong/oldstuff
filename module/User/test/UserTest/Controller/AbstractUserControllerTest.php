<?php
namespace UserTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use User\Entity\User;
use User\Entity\Contact;

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
        
        // First setup
        $this->getEntityManager();
        $this->getUser();
    }
    
    protected function login()
    {
         // Log user in 
        $adapter = $this->getAuthService()->getAdapter();
        $adapter->setIdentityValue(self::EMAIL);
        $adapter->setCredentialValue(self::PASSWORD);
        $this->getAuthService()->authenticate();
    }
    
    protected function logout()
    {
        // Log user out
        $this->getAuthService()->clearIdentity();
    }
    
    protected function getEntityManager()
    {
        if ($this->em === null) {
            $this->em = $this->getApplicationServiceLocator()->get('doctrine.entitymanager.orm_default');
            $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
            $classes = array($this->em->getClassMetadata('User\Entity\User'), $this->em->getClassMetadata('User\Entity\Contact'));
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
            $contact = new Contact();
            $contact->populate(array(
                'contact_id' => 1,
                'address' => '',
                'city' => '',
                'state' => '',
                'zipcode' => '',
                'country' => '',
                'phone' => '',
            ));
            $bcrypt = new \Zend\Crypt\Password\Bcrypt();
            $bcrypt->setCost(4); // lower cost for faster test
            $this->user->populate(array(
                'user_id' => 1,
                'display_name' => 'User',
                'email' => self::EMAIL,
                'password' => $bcrypt->create(self::PASSWORD),
                'state' => 1,
                'contact' => $contact,
            ));
            $this->getEntityManager()->persist($this->user);
            $this->getEntityManager()->flush();
        }
        
        return $this->user;
    }
}