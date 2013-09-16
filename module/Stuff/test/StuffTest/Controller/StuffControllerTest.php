<?php
namespace StuffTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class StuffControllerTest extends  AbstractHttpControllerTestCase{
	protected $traceError = true;
	protected $tool = null;
	protected $classes = null;
	
	public function setUp(){
		$this->setApplicationConfig(\StuffTest\Bootstrap::getConfig());
        parent::setUp();
	}
	
	public function testIndexActionCanBeAccessed(){
		$this->dispatch('/stuff/1');
		$this->assertResponseStatusCode(200);
		$this->assertModuleName('Stuff');
		$this->assertControllerName('Stuff\Controller\Stuff');
		$this->assertControllerClass('StuffController');
		$this->assertMatchedRouteName('stuff');
	}
	
	public function testAddActionCanBeAccessed(){
		$this->dispatch('/stuff/1/add');
		$this->assertResponseStatusCode(200);
		$this->assertModuleName('Stuff');
		$this->assertControllerName('Stuff\Controller\Stuff');
		$this->assertControllerClass('StuffController');
		$this->assertMatchedRouteName('stuff');
	}
	
	public function testAddWithValidInformation()
    {
        $this->resetSchema();
        
        /* ---- Valid register information ---- */
        $postData = array(
            'stuffname' => 'Table',
            'description' => 'A good old table',
            'price' => '200',
        );
        $this->dispatch('/stuff/1', 'POST', $postData);
        
        // Should show successful message
        $this->assertQueryContentRegex("div.alert-success", '/successful/');
    }
	
	public function testAddWithInvalidPrice()
    {
        $this->resetSchema();
        
        /* ---- Invalid email ---- */
        $postData = array(
            'stuffname' => 'abcdef',
            'description' => 'abc1234',
            'price' => 'abc1234'
        );
        
        $this->dispatch('/stuff/1', 'POST', $postData);
        // Should show invalid email message
        $this->assertQueryContentRegex("div.alert-danger", '/The input does not appear to be a float/');
    }
	
	protected function resetSchema()
    {
        if ($this->tool == null) {
            $em = $this->getApplicationServiceLocator()->get('doctrine.entitymanager.orm_default');
            $this->tool = new \Doctrine\ORM\Tools\SchemaTool($em);
            $this->classes = array($em->getClassMetadata('Stuff\Entity\Stuff'));
        }
        
        $this->tool->dropSchema($this->classes);
        $this->tool->createSchema($this->classes);
    }
}
