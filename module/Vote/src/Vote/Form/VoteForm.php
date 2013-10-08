<?php
namespace Vote\Form;

use Zend\Form\Form;

class VoteForm extends Form
{
	public function __construct($name = null)
	{
		parent::__construct($name);

		// $this->setAttribute('method', 'post');

		$this->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'rate_box',
            'options' => array(
                'label' => 'Please choose your rate',
                'value_options' => array(
                    '1' => ' Very Bad',
                    '2' => ' Bad',
                    '3' => ' Fine',
                    '4' => ' Good',
                    '5' => ' Very Good',
                ),
            ),
            'attributes' => array(
                'value' => '1' //set checked to '1'
            )
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