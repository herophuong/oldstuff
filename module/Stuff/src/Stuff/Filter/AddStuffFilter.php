<?php
namespace Stuff\Filter;

use Stuff\Filter\AbstractStuffFilter;
use Zend\InputFilter\FileInput;

class AddStuffFilter extends AbstractStuffFilter {
	protected function getStuffNameFilter(){
		return array(
				'name' => 'stuffname',
				'required' => true,
				'validators' => array(
					array(
	                    'name' => 'NotEmpty',
	                    'options' => array(
	                        'message' => 'Please enter stuff name',
	                    ),
	                    'break_chain_on_failure' => true,
	                ),
					array(
						'name' => 'string_length',
						'options' => array(
							'max' => 100,
							'message' => 'Name should not longer than 100 characters',
						),
					),
				)
			);
	}
	
	protected function getDescriptionFilter(){
		return array(
				'name' => 'description',
				'required' => false,
				'validators' => array(
					array(
	                    'name' => 'NotEmpty',
	                    'options' => array(
	                        'message' => 'Please enter description',
	                    ),
	                    'break_chain_on_failure' => true,
	                ),
				)
			);
	}
	
	protected function getPriceFilter(){
		return array(
				'name' => 'price',
				'required' => false,
				'validators' => array(
					array(
	                    'name' => 'NotEmpty',
	                    'options' => array(
	                        'message' => 'Please enter stuff price',
	                    ),
	                    'break_chain_on_failure' => true,
	                ),
					array(
						'name' => 'Float',
						'options' => array(
							'message' => 'Price must be a number'
						),
					)
				)
			);
	}

    protected function getCategoryFilter(){
        return array(
            'name' => 'category',
            'required' => true,
        );
    }

    protected function getDesiredStuffFilter(){
        return array(
            'name' => 'desiredstuff',
            'required' => false,
        );
    }
    
    protected function getPurposeFilter(){
        return array(
            'name' => 'purpose',
            'required' => true,
        );
    }
    
    protected function getImageFilter(){
        $fileinput = new FileInput("image");
        $fileinput->setRequired(true);
        $fileinput->getValidatorChain()->attachByName("filesize", array("max" => "2MB"))
                                       ->attachByName("fileisimage")
                                       ->attachByName("fileuploadfile");
        return $fileinput;                                     
    }
    
    protected function getStateFilter(){
        return array(
            'name' => 'state',
            'required' => true,
        );
    }
}
