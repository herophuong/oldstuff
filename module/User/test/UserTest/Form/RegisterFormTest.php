<?php
namespace UserTest\Form;

use User\Form\UserForm;
use User\Filter\RegisterFilter;

class RegisterFormTest extends \PHPUnit_Framework_TestCase
{
    protected static $form = null;
    
    public static function setUpBeforeClass() {
        static::$form = new UserForm();
        $filter = new RegisterFilter();
        static::$form->setInputFilter($filter->getInputFilter());
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
    
    public function testLowerCaseAndTrimEmail()
    {
        static::$form->setData(array('email' => 'UseR@ExamPLe.com '));
        
        // Validate data
        static::$form->isValid();
        
        $data = static::$form->getData();
        $this->assertEquals($data['email'], 'user@example.com');
    }
}