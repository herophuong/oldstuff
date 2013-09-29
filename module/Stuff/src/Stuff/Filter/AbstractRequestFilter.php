<?php
namespace Stuff\Filter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

abstract class AbstractRequestFilter implements InputFilterAwareInterface {
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
            if ($this->getPhoneFilter())
                $this->inputFilter->add($this->getPhoneFilter());
            if ($this->getDescriptionFilter())
                $this->inputFilter->add($this->getDescriptionFilter());
            if ($this->getAddressFilter())
                $this->inputFilter->add($this->getAddressFilter());
            if ($this->getPaymentMethodFilter())
                $this->inputFilter->add($this->getPaymentMethodFilter());
        }
        return $this->inputFilter;
    }
    
    /**
* Methods to create input filter
*
* @return array Specification of the filter
*/
    abstract protected function getPhoneFilter();
    abstract protected function getDescriptionFilter();
    abstract protected function getAddressFilter();
    abstract protected function getPaymentMethodFilter();
}
