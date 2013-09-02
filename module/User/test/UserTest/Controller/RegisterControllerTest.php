<?php
namespace UserTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use User\Form\RegisterForm;

class RegisterControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;
    
    public function setUp()
    {
        $this->setApplicationConfig(
            include static::findParentPath('module').'/../config/application.config.php'
        );
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
    
    public function testRegisterLayoutIsRight()
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
    
    public function testRegisterFormValidation()
    {
        $form = new RegisterForm();
        
        // Test valid data
        $form->setData(array(
            'email' => 'user@example.com',
            'password' => 'Lorem Ipsum',
            'passwordconfirmation' => 'Lorem Ipsum',
        ));
        $this->assertTrue($form->isValid());
        
        // Test invalid email
        $form->setData(array(
            'email' => 'ab c @ example.com'
        ));
        $this->assertFalse($form->isValid());
        
        $form->setData(array(
            'email' => 'user@example.com',
            'password' => 'Lorem Ipsum',
            'passwordconfirmation' => 'Ipsum Lorem',
        ));
        $this->assertFalse($form->isValid());
    }
    
    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) return false;
            $previousDir = $dir;
        }
        return $dir . '/' . $path;
    }
}