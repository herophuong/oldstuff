<?php
namespace UserTest\Form;

use User\Form\RegisterForm;

class RegisterFormTest extends \PHPUnit_Framework_TestCase
{
    protected $form = null;
    
    public function setUp() {
        $this->form = new RegisterForm();
    }
    
    public function testFormWithValidData() {
        $this->form->setData(array(
            'email' => 'user@example.com',
            'password' => 'Lorem Ipsum',
            'passwordconfirmation' => 'Lorem Ipsum',
        ));
        $this->assertTrue($this->form->isValid());
    }
    
    public function testFormWithInvalidEmail() {
        $this->form->setData(array(
            'email' => 'abcdef',
            'password' => 'Lorem Ipsum',
            'passwordconfirmation' => 'Lorem Ipsum',
        ));
        
        $this->assertFalse($this->form->isValid());
    }
    
    public function testFormWithUnmatchPassword()
    {        
        $this->form->setData(array(
            'email' => 'user@example.com',
            'password' => 'Lorem Ipsum',
            'passwordconfirmation' => 'Ipsum Lorem',
        ));
        $this->assertFalse($this->form->isValid());
    }
}