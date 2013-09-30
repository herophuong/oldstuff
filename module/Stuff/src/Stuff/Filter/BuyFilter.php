<?php
namespace Stuff\Filter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class BuyFilter implements InputFilterAwareInterface {
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
            if ($this->getPaymentMethodFilter())
                $this->inputFilter->add($this->getPaymentMethodFilter());
        }
        return $this->inputFilter;
    }

    protected function getPaymentMethodFilter(){
        return array(
            'name' => 'paymentmethod',
            'required' => true,
        );
    }
}
