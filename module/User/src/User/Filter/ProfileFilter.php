<?php
namespace User\Filter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ProfileFilter implements InputFilterAwareInterface
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
            $this->addEmailFilter();
            $this->addPasswordConfirmFilter();
            $this->addDisplayNameFilter();
        }
        
        return $this->inputFilter;
    }
    
    protected function addEmailFilter()
    {
        $this->inputFilter->add(array(
            'name' => 'email',
            'required' => false,
            'filters' => array(
                array('name' => 'StringToLower'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array('name' => 'EmailAddress'),
            ),
        ));
    }
    
    protected function addPasswordConfirmFilter()
    {
        $this->inputFilter->add(array(
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