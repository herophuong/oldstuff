<?php
namespace Vote\Form;

use Zend\Form\Form;

class VoteForm extends Form
{
	public function __construct($name = null)
	{
		parent::__construct($name);

		$this->add(array(
			'name' => 'rate_box',
			'type' => 'Text',
			'options' => array(
				'label' => 'Enter your rate here',
				'label_attributes' => array(
					'class' => 'control-label col-lg-4',
				),
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