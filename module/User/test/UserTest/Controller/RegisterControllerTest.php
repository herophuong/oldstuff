<?php
namespace UserTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use User\Form\RegisterForm;

class RegisterControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;
    protected $tool = null;
    protected $classes = null;
    
    public function setUp()
    {
        $this->setApplicationConfig(\UserTest\Bootstrap::getConfig());
        parent::setUp();
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
    
    public function testRegisterLayout()
    {
        $this->dispatch('/register');
        
        // This should has Register title
        $this->assertQueryContentContains("h3", "Register");
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
        $this->resetSchema();
        
        /* ---- Valid register information ---- */
        $postData = array(
            'email' => 'user@example.com',
            'password' => 'abcd1234',
            'passwordconfirmation' => 'abcd1234',
        );
        $this->dispatch('/register', 'POST', $postData);
        
        // Should show successful message
        $this->assertQueryContentRegex("div.alert-success", '/successful/');
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
        $this->assertQueryContentRegex("div.alert-danger", '/Please provide a valid email/');
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
        $this->assertQueryContentRegex("div.alert-danger", '/Your passwords are not matched/');
    }
    
    protected function resetSchema()
    {
        if ($this->tool == null) {
            $em = $this->getApplicationServiceLocator()->get('doctrine.entitymanager.orm_default');
            $this->tool = new \Doctrine\ORM\Tools\SchemaTool($em);
            $this->classes = array($em->getClassMetadata('User\Entity\User'));
        }
        
        $this->tool->dropSchema($this->classes);
        $this->tool->createSchema($this->classes);
    }
}