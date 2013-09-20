<?php
namespace UserTest\Controller;

use UserTest\Controller\AbstractUserControllerTest;

class RegisterControllerTest extends AbstractUserControllerTest
{
    public function setUp()
    {
        parent::setUp();
        
        // Mark transaction
        $this->em->beginTransaction();
    }
    
    public function testRegisterActionCanBeAccessed()
    {
        $this->dispatch('/register');
        $this->assertResponseStatusCode(200);
        
        $this->assertModuleName('User');
        $this->assertControllerName('User\Controller\User');
        $this->assertControllerClass('UserController');
        $this->assertMatchedRouteName('register');
    }
    
    public function testRegisterActionShouldNotBeAccessedByLoggedInUser()
    {
        // Log user in 
        $this->login();
        
        // Make sure response is redirect
        $this->dispatch('/register');
        $this->assertRedirect();
    }
    
    public function testRegisterLayout()
    {
        $this->dispatch('/register');
        
        // This should has Register title
        $this->assertQueryContentRegex("title", '/Register/');
        
        // This should has a form
        $this->assertQuery("form");
        $this->assertQuery('input[name="email"]');
        $this->assertQuery('input[name="password"]');
        $this->assertQuery('input[name="passwordconfirmation"]');
        $this->assertQuery('input[type="submit"]');
    }
    
    public function testRegisterWithValidInformation()
    {        
        /* ---- Valid register information ---- */
        $postData = array(
            'email' => 'user2@example.com',
            'password' => self::PASSWORD,
            'passwordconfirmation' => self::PASSWORD,
        );
        $this->dispatch('/register', 'POST', $postData);
        
        // Should show successful message
        $this->assertQueryContentRegex("div.alert-success", '/successful/');
        
        // Make sure the user is stored
        $repository = $this->getEntityManager()->getRepository('User\Entity\User');
        $user = $repository->findOneBy(array('email' => self::EMAIL));
        $this->assertTrue($user instanceof \User\Entity\User);
    }
    
    public function testRegisterWithInvalidEmail()
    {        
        /* ---- Invalid email ---- */
        $postData = array(
            'email' => 'abcdef',
            'password' => 'abc1234',
            'passwordconfirmation' => 'abc1234'
        );
        
        $this->dispatch('/register', 'POST', $postData);
        // Should show invalid email message
        $this->assertQueryContentRegex("div.alert-danger", '/provide a valid email/');
    }
    
    public function testRegisterWithDuplicatedEmail()
    {
        $postData = array(
            'email' => self::EMAIL,
            'password' => self::PASSWORD,
            'passwordconfirmation' => self::PASSWORD,
        );
        
        $this->dispatch('/register', 'POST', $postData);
        // Should show invalid message
        $this->assertQuery("div.alert-danger");
    }
    
    public function testRegisterWithUnmatchPassword()
    {        
        $postData = array(
            'email' => 'user@example.com',
            'password' => 'abc1234',
            'passwordconfirmation' => '4321cba',
        );
        
        $this->dispatch('/register', 'POST', $postData);
        // Should show unmatch password message
        $this->assertQueryContentRegex("div.alert-danger", '/not matched/');
    }
    
    public function tearDown()
    {
        // Roll back all changes
        $this->em->rollback();
    }
}