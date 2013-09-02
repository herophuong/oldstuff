<?php
namespace User\Filter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class RegisterFilter implements InputFilterAwareInterface
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
            
            $inputFilter->add(array(
                'name' => 'email',
                'required' => true,
            ));
            
            $inputFilter->add(array(
                'name' => 'password',
                'required' => true,
            ));
            
            $inputFilter->add(array(
                'name' => 'passwordconfirmation',
                'required' => true,
                'validators' => array(
                    array(
                    'name' => 'Identical',
                        'options' => array(
                            'token' => 'password' //I have tried $_POST['password'], but it doesnt work either
                        )
                    )
                ),
            ));
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }
}