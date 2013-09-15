<?php
namespace UserTest\Controller;

use UserTest\Controller\AbstractUserControllerTest;
use User\Entity\User;

class EditControllerTest extends AbstractUserControllerTest
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
        
        // Mark transaction
        $this->em->beginTransaction();
    }
    
    public function testEditPage()
    {
        $this->dispatch('/user/edit/'.$this->user->user_id);
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('User');
        $this->assertControllerName('User\Controller\User');
        $this->assertControllerClass('UserController');
        $this->assertMatchedRouteName('user');
        
        // Make sure an edit form exists
        $this->assertQuery('form');
    }
    
    public function testEditPageShouldNotBeAccessByAnonymous()
    {
        // Clear the identity
        $this->authService->clearIdentity();
        
        // Make sure we have redirect anonymous user from this route
        $this->dispatch('/user/edit/'.$this->user->user_id);
        $this->assertRedirect();
    }
    
    public function testEditPageShouldNotBeAccessByAnotherUser()
    {
        // Create new user
        $user = new User();
        $bcrypt = new \Zend\Crypt\Password\Bcrypt();
        $bcrypt->setCost(4); // lower cost for faster test
        $user->populate(array(
            'user_id' => 2,
            'display_name' => 'User 2',
            'email' => 'user2@example.com',
            'password' => $bcrypt->create('test'),
            'state' => 1,
        ));
        $this->em->persist($user);
        $this->em->flush();
        
        // Log in as new user
        $adapter = $this->authService->getAdapter();
        $adapter->setIdentityValue('user2@example.com');
        $adapter->setCredentialValue('test');
        $this->authService->authenticate();
        
        // Now test
        $this->dispatch('/user/edit/'.$this->user->user_id);
        $this->assertRedirect();
    }
    
    public function testChangeDisplayName()
    {
        $data['display_name'] = 'Another User';
        $this->dispatch('/user/edit/'.$this->user->user_id, 'POST', $data);
        
        // Clear identity map
        $this->em->clear();
        
        // Get the user from the database
        $user = $this->em->find('User\Entity\User', $this->user->user_id);
        
        // Make sure the name is changed
        $this->assertEquals($data['display_name'], $user->display_name);
        
        // And redirect to the profile page on success
        $this->assertRedirectTo('/user/profile/'.$user->user_id);
    }
    
    public function testChangePassword()
    {
        $oldpassword = $this->user->password;
        $data['password'] = 'New Password';
        $data['passwordconfirmation'] = 'New Password';
        $this->dispatch('/user/edit/'.$this->user->user_id, 'POST', $data);
        
        // Clear identity map
        $this->em->clear();
        
        // Get the user from the database
        $user = $this->em->find('User\Entity\User', $this->user->user_id);
        
        // Make sure the password is changed
        $this->assertNotEquals($oldpassword, $user->password);
    }
    
    public function testEmptyPasswordConfirmationBug()
    {
        $oldpassword = $this->user->password;
        $data['password'] = 'New Password';
        $data['passwordconfirmation'] = '';
        $this->dispatch('/user/edit/'.$this->user->user_id, 'POST', $data);
        
        // Clear identity map
        $this->em->clear();
        
        // Get the user from the database
        $user = $this->em->find('User\Entity\User', $this->user->user_id);
        
        // Make sure the password is unchanged
        $this->assertEquals($oldpassword, $user->password);
    }
    
    public function testUnspecifiedIdPage()
    {
        $this->dispatch('/user/edit');
        $this->assertResponseStatusCode(404);
    }
    
    public function tearDown()
    {
        // Rollback all changes made during the test
        $this->em->rollback();
    }
}