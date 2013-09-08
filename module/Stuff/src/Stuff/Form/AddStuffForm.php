<?php
namespace Stuff\Form;

use Zend\Form\Form;


class AddStuffForm extends Form {
	
	public function __construct($name = null) {
		parent::__construct('stuff');
		
		$this->add(array(
			'name' => 'stuffname',
			'type' => 'Text',
			'options' => array(
				'label' => 'Name',
				'label_attributes' => array(
					'class' => 'control-label col-lg-4',
				),
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
				'label_attributes' => array(
					'class' => 'control-label col-lg-4',
				),
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
				'label_attributes' => array(
					'class' => 'control-label col-lg-4',
				),
			),
			'attributes' => array(
				'id' => 'inputprice',
				'class' => 'form-control',
			),
		));
		
		$this->add(array(
			'name' => 'catname',
			'type' => 'Text',
			'option' => array(
				'label' => 'Category',
				'label_attributes' => array(
					'class' => 'control-label col-lg-4',
				),
			),
			'attributes' => array(
				'id' => 'inputcatname',
				'class' => 'form-control',
			),
		));
		
		$this->add(array(
			'name' => 'submit',
			'type' => 'Submit',
			'attributes' => array(
				'id' => 'submit',
				'value' => 'Add',
				'class' => 'btn btn-primary',
			),
		));
	}
}
