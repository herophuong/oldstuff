<?php
namespace Stuff\Filter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class TradeFilter implements InputFilterAwareInterface {
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
            if ($this->getExchangeStuffFilter())
                $this->inputFilter->add($this->getExchangeStuffFilter());
        }
        return $this->inputFilter;
    }

    protected function getExchangeStuffFilter(){
        return array(
            'name' => 'proposed_stuff',
            'required' => true,
        );
    }
}
