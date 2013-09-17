<?php
namespace User\Filter;

use User\Filter\AbstractUserFilter;

class ProfileFilter extends AbstractUserFilter
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
            'required' => false,
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
        return null;
    }
    
    protected function getPasswordConfirmationFilter()
    {
        return array(
            'name' => 'passwordconfirmation',
            'required' => false,
            'validators' => array(
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