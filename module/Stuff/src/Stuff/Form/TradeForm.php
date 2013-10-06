<?php
namespace Stuff\Form;

use Zend\Form\Form;


class TradeForm extends Form {
    
    public function __construct($name = null) {
        parent::__construct($name);
        $this->add(array(
            'name' => 'proposed_stuff',
            'type' => 'Select',
            'options' => array(
                'label' => 'Propose stuff',
                'value_options' => array(
                    '0' => '--Select your stuff--'
                )
            ),
            'attributes' => array(
                'id' => 'exchangeStuff',
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