<?php
namespace User\Form;

use Zend\Form\Form;

class UserForm extends Form
{
    public function __construct($name = null)
    {
        // Ignore all names passing here
        parent::__construct($name);
        
        $this->add(array(
            'name' => 'display_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Display Name',
            ),
        ));
        $this->add(array(
            'name' => 'email', 
            'type' => 'Text', 
            'options' => array(
                'label' => 'Email',
            ),
            'attributes' => array(
                'id' => 'inputemail',
                'required' => 'required',
            ),
        ));
        $this->add(array(
            'name' => 'password', 
            'type' => 'Password',
            'options' => array(
                'label' => 'Password',
            ),
            'attributes' => array(
                'id' => 'inputpassword',
            ),
        ));
        $this->add(array(
            'name' => 'passwordconfirmation', 
            'type' => 'Password',
            'options' => array(
                'label' => 'Re-type Password',
            ),
            'attributes' => array(
                'id' => 'inputpasswordconfirm',
            ),
        ));
        $this->add(array(
            'name' => 'submit', 
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Sign up',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            ),
        ));
    }
}