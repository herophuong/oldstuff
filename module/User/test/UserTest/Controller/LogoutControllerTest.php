<?php
namespace UserTest\Controller;

use UserTest\Controller\AbstractUserControllerTest;

class LogoutControllerTest extends AbstractUserControllerTest
{
    public function setUp()
    {
        parent::setUp();
        
        // Log user in 
        $this->login();
    }
    
    public function testLogoutAction()
    {
        $this->dispatch('/logout');
        
        $this->assertNull($this->getAuthService()->getIdentity());
        $this->assertRedirect(); // Should redirect after logout
    }
}