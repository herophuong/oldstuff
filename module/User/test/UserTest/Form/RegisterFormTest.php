<?php
namespace UserTest\Form;

use User\Form\RegisterForm;

class RegisterFormTest extends \PHPUnit_Framework_TestCase
{
    protected static $form = null;
    
    public static function setUpBeforeClass() {
        static::$form = new RegisterForm();
    }
    
    public function testFormWithValidData() {
        static::$form->setData(array(
            'email' => 'user@example.com',
            'password' => 'Lorem Ipsum',
            'passwordconfirmation' => 'Lorem Ipsum',
        ));
        $this->assertTrue(static::$form->isValid());
    }
    
    public function testFormWithInvalidEmail() {
        static::$form->setData(array(
            'email' => 'abcdef',
            'password' => 'Lorem Ipsum',
            'passwordconfirmation' => 'Lorem Ipsum',
        ));
        
        $this->assertFalse(static::$form->isValid());
    }
    
    public function testFormWithUnmatchPassword()
    {        
        static::$form->setData(array(
            'email' => 'user@example.com',
            'password' => 'Lorem Ipsum',
            'passwordconfirmation' => 'Ipsum Lorem',
        ));
        $this->assertFalse(static::$form->isValid());
    }
}