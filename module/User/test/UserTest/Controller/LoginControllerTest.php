<?php
namespace UserTest\Controller;

use UserTest\Controller\AbstractUserControllerTest;
use Zend\View\Helper\Url as UrlHelper;

class LoginControllerTest extends AbstractUserControllerTest
{    
    public function testLoginLayout()
    {
        $this->dispatch('/login');
        
        // At least should has these fields
        $this->assertQuery('input[name="email"]');
        $this->assertQuery('input[name="password"]');
    }
    
    public function testLoginWithValidCredential()
    {
        $data['email'] = self::EMAIL;
        $data['password'] = self::PASSWORD;
        $this->dispatch('/login', 'POST', $data);
        
        // Make sure the system has identity
        $this->assertTrue($this->getAuthService()->getIdentity() !== null);
    }
    
    public function testLoginWithUnsignedEmail()
    {
        $data['email'] = 'abcdef@example.com';
        $data['password'] = self::PASSWORD;
        $this->dispatch('/login', 'POST', $data);
        
        // Make sure some error messages appear
        $this->assertQuery('div.alert-danger');
    }
    
    public function testLoginWithInvalidEmail()
    {
        $data['email'] = 'abcdef';
        $data['password'] = self::PASSWORD;
        $this->dispatch('/login', 'POST', $data);
        
        // Make sure some error messages appear
        $this->assertQuery('div.alert-danger');
    }
    
    public function testLoginWithInvalidPassword()
    {
        $data['email'] = self::EMAIL;
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
    
    public function testLoginActionShouldRedirectIfAlreadyLoggedIn()
    {
        $adapter = $this->getAuthService()->getAdapter();
        $adapter->setIdentityValue(self::EMAIL);
        $adapter->setCredentialValue(self::PASSWORD);
        $this->authService->authenticate();
        
        $this->dispatch('/login');
        $this->assertRedirect();
    }
    
    public function testLoginRedirectionWithParameter()
    {
        $data['email'] = self::EMAIL;
        $data['password'] = self::PASSWORD;
        $this->dispatch('/login?redirect=/', 'POST', $data);
        
        $this->assertRedirect();
        // TODO Test this
//         $this->assertMatchedRouteName('home');
    }
}
    