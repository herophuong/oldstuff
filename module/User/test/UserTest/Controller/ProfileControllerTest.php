<?php
namespace UserTest\Controller;

use UserTest\Controller\AbstractUserControllerTest;

class ProfileControllerTest extends AbstractUserControllerTest
{    
    public function testProfilePage()
    {
        $this->dispatch('/user/profile/'.$this->user->user_id);
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('User');
        $this->assertControllerName('User\Controller\User');
        $this->assertControllerClass('UserController');
        $this->assertMatchedRouteName('user');
        
        // Search for user information in the body
        $this->assertQueryContentRegex('body', '/'.$this->user->email.'/');
        $this->assertQueryContentRegex('body', '/'.$this->user->display_name.'/');
    }
    
    public function testUnspecifiedIdPage()
    {
        $this->dispatch('/user/profile');
        $this->assertResponseStatusCode(404);
    }
}