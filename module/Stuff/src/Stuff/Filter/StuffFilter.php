<?php
namespace Stuff\Filter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class StuffFilter implements InputFilterAwareInterface {
	protected $inputfilter;
	
	public function setInputFilter(InputFilterInterface	$inputfilter){
        throw new Exception("Not used");
	}
	
	public function getInputFilter(){
		if (!$this->inputfilter) {
			$inputfilter = new InputFilter();
			
			$inputfilter->add(array(
				'name' => 'stuffname',
				'required' => 'true',
				'validators' => array(
					array(
						'name' => 'string_length',
						'options' => array(
							'max' => 100
						),
					),
				)
			));
			
			$inputfilter->add(array(
				'name' => 'description',
				'required' => 'false',
				'validators' => array(
					array(
						'name' => 'string_length',
						'options' => array(
							'max' => 300
						),
					),
				)
			));
			
			$inputfilter->add(array(
				'name' => 'price',
				'required' => 'true',
				'validators' => array(
					array(
						'name' => 'Float',
					)
				)
			));
			
			$this->inputfilter = $inputfilter;
		}
		
		return $this->inputfilter;
	}
}
