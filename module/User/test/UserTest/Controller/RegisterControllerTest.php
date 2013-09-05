<?php
namespace UserTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use User\Form\RegisterForm;

class RegisterControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;
    protected $tool = null;
    
    public function setUp()
    {
        $this->setApplicationConfig(\UserTest\Bootstrap::getConfig());
        
        $em = $this->getApplicationServiceLocator()->get('doctrine.entitymanager.orm_default');
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = array($em->getClassMetadata('User\Entity\User'));
        $tool->dropSchema($classes);
        $tool->createSchema($classes);
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
    
    public function testRegisterAction()
    {
//         $postData = array(
//             'email' => 'user@example.com',
//             'password' => 'abcd1234',
//             'passwordconfirmation' => 'abcd1234',
//         );
//         
//         $this->dispatch('/register', 'POST', $postData);
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