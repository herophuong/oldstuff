<?php
namespace User\Filter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ContactFilter implements InputFilterAwareInterface
{
    protected $inputFilter;
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new Exception("Not used");
    }
    
    public function getInputFilter()
    {
        if ($this->inputFilter == null) {
            $this->inputFilter = new InputFilter();
            
            $this->inputFilter->add(array(
                'name' => 'address',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                ),
            ));
            $this->inputFilter->add(array(
                'name' => 'city',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                ),
            ));
            $this->inputFilter->add(array(
                'name' => 'state',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                ),
            ));
            $this->inputFilter->add(array(
                'name' => 'zipcode',
                'required' => false,
                'validators' => array(
                    array('name' => 'AlNum'),
                ),
            ));
            $this->inputFilter->add(array(
                'name' => 'phone',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                ),
                'validators' => array(
                    array('name' => 'PhoneNumber', 'options' => array('country' => 'VN')),
                ),
            ));
            $this->inputFilter->add(array(
                'name' => 'country',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                ),
            ));
        }
        
        return $this->inputFilter;
    }
}