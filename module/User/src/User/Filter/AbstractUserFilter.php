<?php
namespace User\Filter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

abstract class AbstractUserFilter implements InputFilterAwareInterface
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
            if ($this->getDisplayNameFilter())
                $this->inputFilter->add($this->getDisplayNameFilter());
            if ($this->getEmailFilter())
                $this->inputFilter->add($this->getEmailFilter());
            if ($this->getPasswordFilter())
                $this->inputFilter->add($this->getPasswordFilter());
            if ($this->getPasswordConfirmationFilter())
                $this->inputFilter->add($this->getPasswordConfirmationFilter());
        }
        
        return $this->inputFilter;
    }
    
    /**
     * Methods to create input filter
     *
     * @return array Specification of the filter
     */
    abstract protected function getDisplayNameFilter();
    abstract protected function getEmailFilter();
    abstract protected function getPasswordFilter();
    abstract protected function getPasswordConfirmationFilter();
}