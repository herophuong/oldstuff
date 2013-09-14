<?php
namespace User\Filter;

use User\Filter\AbstractUserFilter;

class RegisterFilter extends AbstractUserFilter
{
    protected function getDisplayNameFilter()
    {
        return array(
            'name' => 'display_name',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StripTags',
                ),
            ),
        );
    }
    
    protected function getEmailFilter()
    {
        return array(
            'name' => 'email',
            'required' => true,
            'filters' => array(
                array(
                    'name' => 'StringToLower',
                ),
                array(
                    'name' => 'StringTrim',
                ),
            ),
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'message' => 'Please enter your email!',
                    ),
                    'break_chain_on_failure' => true,
                ),
                array(
                    'name' => 'EmailAddress',
                    'options' => array(
                        'message' => 'Please provide a valid email!',
                    ),
                ),
            ),
        );
    }
    
    protected function getPasswordFilter()
    {
        return array(
            'name' => 'password',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'message' => 'Please enter your password!',
                    ),
                ),
            ),
        );
    }
    
    protected function getPasswordConfirmationFilter()
    {
        return array(
            'name' => 'passwordconfirmation',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'NotEmpty',
                    'options' => array(
                        'message' => 'Please confirm your password!',
                    ),
                    'break_chain_on_failure' => true,
                ),
                array(
                    'name' => 'Identical',
                    'options' => array(
                        'token' => 'password',
                        'message' => 'Your password fields are not matched!'
                    ),
                ),
            ),
        );
    }
}