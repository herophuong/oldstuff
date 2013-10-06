<?php
namespace Stuff\Form;

use Zend\Form\Form;


class StuffForm extends Form {
	
	public function __construct($name = null) {
		parent::__construct('stuff');
		
		$this->add(array(
			'name' => 'stuffname',
			'type' => 'Text',
			'options' => array(
				'label' => 'Name',
			),
			'attributes' => array(
				'id' => 'inputstuffname',
				'class' => 'form-control',
			),
		));
		
		$this->add(array(
			'name' => 'description',
			'type' => 'TextArea',
			'options' => array(
				'label' => 'Description',
			),
			'attributes' => array(
				'id' => 'inputdescription',
				'class' => 'form-control',
			),
		));
		
		$this->add(array(
			'name' => 'price',
			'type' => 'Text',
			'options' => array(
				'label' => 'Price',
			),
			'attributes' => array(
				'id' => 'inputprice',
				'class' => 'form-control',
			),
		));
		
		$this->add(array(
			'name' => 'category',
			'type' => 'Select',
			'options' => array(
				'label' => 'Category',
			),
			'attributes' => array(
				'id' => 'inputcategory',
				'class' => 'form-control',
			),
		));
		
        $this->add(array(
            'name' => 'desiredstuff',
            'type' => 'Text',
            'options' => array(
                'label' => 'Desired Stuff',
            ),
            'attributes' => array(
                'id' => 'inputdesiredstuff',
                'class' => 'form-control',
            ),
        ));
        
        $this->add(array(
            'name' => 'purpose',
            'type' => 'Select',
            'options' => array(
                'label' => 'Purpose',
                'value_options' => array(
                    'sell' => 'Sell',
                    'trade' => 'Trade',
                    'sell,trade' => 'Sell/Trade',
                ),
            ),
            'value' => 'sell',
            'attributes' => array(
                'id' => 'inputpurpose',
                'class' => 'form-control',
            ),
        ));
        
        $this->add(array(
            'name' => 'image',
            'type' => 'File',
            'options' => array(
                'label' => 'Image',
            ),
            'attributes' => array(
                'id' => 'inputimage',
                'class' => 'form-control',
            ),
        ));
        
        $this->add(array(
            'name' => 'state',
            'type' => 'Select',
            'options' => array(
                'label' => 'State',
                'value_options' => array(
                    '1' => 'Publish',
                    '0' => 'Unpublish',
                )
            ),
            'attributes' => array(
                'id' => 'inputstate',
                'class' => 'form-control',
            ),
        ));
        
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'id' => 'submit',
				'class' => 'btn btn-primary',
			),
		));
        
	}
}