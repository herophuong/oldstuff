<?php
namespace UserTest\Controller;

use UserTest\Controller\AbstractUserControllerTest;

class LogoutControllerTest extends AbstractUserControllerTest
{
    public function setUp()
    {
        parent::setUp();
        
        // Log user in 
        $this->authService = $this->getApplicationServiceLocator()->get('Zend\Authentication\AuthenticationService');
        $adapter = $this->authService->getAdapter();
        $adapter->setIdentityValue(self::EMAIL);
        $adapter->setCredentialValue(self::PASSWORD);
        $this->authService->authenticate();
    }
    
    public function testLogoutAction()
    {
        $this->dispatch('/logout');
        
        $this->assertNull($this->authService->getIdentity());
        $this->assertRedirect(); // Should redirect after logout
    }
}