<?php
namespace UserTest\Controller;

use UserTest\Controller\AbstractUserControllerTest;
use User\Entity\User;

class EditControllerTest extends AbstractUserControllerTest
{    
    public function setUp()
    {
        parent::setUp();
        
        $this->login();
        
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
        // Log out first
        $this->logout();
        
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
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        
        // Log in as new user
        $adapter = $this->getAuthService()->getAdapter();
        $adapter->setIdentityValue('user2@example.com');
        $adapter->setCredentialValue('test');
        $this->getAuthService()->authenticate();
        
        // Now test
        $this->dispatch('/user/edit/'.$this->getUser()->user_id);
        $this->assertRedirect();
    }
    
    public function testChangeDisplayName()
    {
        $data['display_name'] = 'Another User';
        $this->dispatch('/user/edit/'.$this->getUser()->user_id, 'POST', $data);
        
        // Clear identity map
        $this->getEntityManager()->clear();
        
        // Get the user from the database
        $user = $this->getEntityManager()->find('User\Entity\User', $this->getUser()->user_id);
        
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
        $this->dispatch('/user/edit/'.$this->getUser()->user_id, 'POST', $data);
        
        // Clear identity map
        $this->getEntityManager()->clear();
        
        // Get the user from the database
        $user = $this->getEntityManager()->find('User\Entity\User', $this->getUser()->user_id);
        
        // Make sure the password is changed
        $this->assertNotEquals($oldpassword, $user->password);
    }
    
    public function testEmptyPasswordConfirmationBug()
    {
        $oldpassword = $this->getUser()->password;
        $data['password'] = 'New Password';
        $data['passwordconfirmation'] = '';
        $this->dispatch('/user/edit/'.$this->getUser()->user_id, 'POST', $data);
        
        // Clear identity map
        $this->getEntityManager()->clear();
        
        // Get the user from the database
        $user = $this->getEntityManager()->find('User\Entity\User', $this->getUser()->user_id);
        
        // Make sure the password is unchanged
        $this->assertEquals($oldpassword, $user->password);
    }
    
    public function testChangeContact()
    {
        $contact['address'] = '20 Ton That Tung';
        $contact['city'] = 'Dong Dat';
        $contact['state'] = 'Ha Noi';
        $contact['country'] = 'Vietnam';
        $contact['zipcode'] = '21000';
        $contact['phone'] = '0988373748';
        $this->dispatch('/user/edit/'.$this->getUser()->user_id, 'POST', array('contact' => $contact));
        
        // Clear identity map
        $this->getEntityManager()->clear();
        
        // Get the user from the database
        $currentContact = $this->getEntityManager()->find('User\Entity\User', $this->getUser()->user_id)->contact;
        
        // Make sure the contact is changed
        $currentContact = $currentContact->getArrayCopy();
        $this->assertEquals($contact['address'], $currentContact['address']);
        $this->assertEquals($contact['city'], $currentContact['city']);
        $this->assertEquals($contact['state'], $currentContact['state']);
        $this->assertEquals($contact['zipcode'], $currentContact['zipcode']);
        $this->assertEquals($contact['country'], $currentContact['country']);
        $this->assertEquals($contact['phone'], $currentContact['phone']);
    }
    
    public function testChangeInvalidPhone()
    {
        $contact['phone'] = 'abcxyz';
        $this->dispatch('/user/edit/'.$this->getUser()->user_id, 'POST', array('contact' => $contact));
        
        // Clear identity map
        $this->getEntityManager()->clear();
        
        // Get the user from the database
        $currentContact = $this->getEntityManager()->find('User\Entity\User', $this->getUser()->user_id)->contact->getArrayCopy();
        
        // Make sure the contact is unchanged
        $this->assertNotEquals($contact['phone'], $currentContact['phone']);
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