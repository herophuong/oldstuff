<?php
namespace UserTest\Form;

use User\Form\RegisterForm;

class RegisterFormTest extends \PHPUnit_Framework_TestCase
{
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
}