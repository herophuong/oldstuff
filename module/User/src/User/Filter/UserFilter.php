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
            
            $emailvalidator = new EmailAddress();
            $emailvalidator->setMessage("Please provide a valid email");
            $inputFilter->add(array(
                'name' => 'email',
                'required' => true,
                'validators' => array(
                    $emailvalidator,
                ),
            ));
            
            $inputFilter->add(array(
                'name' => 'password',
                'required' => true,
            ));
            
            $identicalvalidator = new Identical();
            $identicalvalidator->setMessage("Your passwords are not matched!", Identical::NOT_SAME);
            $identicalvalidator->setToken('password');
            $inputFilter->add(array(
                'name' => 'passwordconfirmation',
                'required' => true,
                'validators' => array(
                    $identicalvalidator,
                ),
            ));
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }
}