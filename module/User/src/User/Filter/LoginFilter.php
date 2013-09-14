<?php
namespace User\Filter;

use User\Filter\AbstractUserFilter;

class LoginFilter extends AbstractUserFilter
{
    protected function getDisplayNameFilter()
    {
        return null;
    }
    
    protected function getEmailFilter()
    {
        return array(
            'name' => 'email',
            'required' => true,
            'filters' => array(
                array('name' => 'StringToLower'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array('name' => 'EmailAddress'),
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
        return null;
    }
}