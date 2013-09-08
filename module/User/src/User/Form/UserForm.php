<?php
namespace User\Form;

use Zend\Form\Form;
use User\Filter\UserFilter;

class UserForm extends Form
{
    public function __construct($name = null)
    {
        // Ignore all names passing here
        parent::__construct('register');
        
        $this->add(array(
            'name' => 'display_name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Display Name',
                'label_attributes' => array(
                    'class' => 'control-label col-lg-4',
                ),
            ),
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'email', 
            'type' => 'Email', 
            'options' => array(
                'label' => 'Email *',
                'label_attributes' => array(
                    'class'  => 'control-label col-lg-4',
                ),
            ),
            'attributes' => array(
                'id' => 'inputemail',
                'class' => 'form-control',
                'aria-required' => 'true',
                'required' => 'required',
            ),
        ));
        $this->add(array(
            'name' => 'password', 
            'type' => 'Password',
            'options' => array(
                'label' => 'Password',
                'label_attributes' => array(
                    'class'  => 'control-label col-lg-4',
                ),
            ),
            'attributes' => array(
                'id' => 'inputpassword',
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'passwordconfirmation', 
            'type' => 'Password',
            'options' => array(
                'label' => 'Re-type Password',
                'label_attributes' => array(
                    'class'  => 'control-label col-lg-4',
                ),
            ),
            'attributes' => array(
                'id' => 'inputpasswordconfirm',
                'class' => 'form-control',
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
        
        $filter = new UserFilter();
        $this->setInputFilter($filter->getInputFilter());
    }
}