<?php
namespace Stuff\Form;

use Zend\Form\Form;


class RequestForm extends Form {
    
    public function __construct($name = null) {
        parent::__construct($name);
        
        $this->add(array(
            'name' => 'address',
            'type' => 'Text',
            'options' => array(
                'label' => 'Address',
            ),
            'attributes' => array(
                'id' => 'inputaddress',
                'class' => 'form-control',
            ),
        ));
        
        $this->add(array(
            'name' => 'phone',
            'type' => 'Text',
            'options' => array(
                'label' => 'Phone',
            ),
            'attributes' => array(
                'id' => 'inputphone',
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
            'name' => 'paymentmethod',
            'type' => 'Select',
            'options' => array(
                'label' => 'Payment Method',
                'value_options' => array(
                    'cash' => 'Cash',
                    'card' => 'Credit Card'
                )
            ),
            'attributes' => array(
                'id' => 'inputpaymentmethod',
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