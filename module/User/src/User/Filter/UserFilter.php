<?php
namespace User\Filter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\EmailAddress;
use Zend\Validator\Identical;

class UserFilter implements InputFilterAwareInterface
{
    protected $inputFilter;
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new Exception("Not used");
    }
    
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            
            $this->inputFilter = $inputFilter;
        }
        
        $this->addEmailFilter();
        $this->addPasswordFilter();
        $this->addPasswordConfirmFilter();
        $this->addDisplayNameFilter();
        
        return $this->inputFilter;
    }
    
    protected function addEmailFilter()
    {
        $this->inputFilter->add(array(
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
        ));
    }
    
    protected function addPasswordFilter()
    {
        $this->inputFilter->add(array(
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
        ));
    }
    
    protected function addPasswordConfirmFilter()
    {
        $this->inputFilter->add(array(
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
        ));
    }
    
    protected function addDisplayNameFilter()
    {
        $this->inputFilter->add(array(
            'name' => 'display_name',
            'required' => false,
            'filters' => array(
                array(
                    'name' => 'StripTags',
                ),
            ),
        ));
    }
}