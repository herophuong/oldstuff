<?php
namespace Stuff\Filter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

abstract class AbstractStuffFilter implements InputFilterAwareInterface {
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
            if ($this->getStuffNameFilter())
                $this->inputFilter->add($this->getStuffNameFilter());
            if ($this->getDescriptionFilter())
                $this->inputFilter->add($this->getDescriptionFilter());
            if ($this->getPriceFilter())
                $this->inputFilter->add($this->getPriceFilter());
            if ($this->getCategoryFilter())
                $this->inputFilter->add($this->getCategoryFilter());
            if ($this->getDesiredStuffFilter())
                $this->inputFilter->add($this->getDesiredStuffFilter());
            if ($this->getPurposeFilter())
                $this->inputFilter->add($this->getPurposeFilter());
            if ($this->getImageFilter())
                $this->inputFilter->add($this->getImageFilter());
            if ($this->getStateFilter())
                $this->inputFilter->add($this->getStateFilter());
        }
        return $this->inputFilter;
    }
    
    /**
* Methods to create input filter
*
* @return array Specification of the filter
*/
    abstract protected function getStuffNameFilter();
    abstract protected function getDescriptionFilter();
    abstract protected function getPriceFilter();
    abstract protected function getCategoryFilter();
    abstract protected function getDesiredStuffFilter();
    abstract protected function getImageFilter();
    abstract protected function getStateFilter();
}
