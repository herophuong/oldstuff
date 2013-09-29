<?php
namespace User\Form;

use Zend\Form\Form;

class ContactForm extends Form
{
    public function __construct($name = null)
    {
        // Ignore all names passing here
        parent::__construct($name);
        
        $this->add(array(
            'name' => 'address',
            'type' => 'Text',
            'options' => array(
                'label' => 'Address',
            ),
        ));
        $this->add(array(
            'name' => 'city', 
            'type' => 'Text', 
            'options' => array(
                'label' => 'City',
            ),
        ));
        $this->add(array(
            'name' => 'state', 
            'type' => 'Text', 
            'options' => array(
                'label' => 'State',
            ),
        ));
        $this->add(array(
            'name' => 'zipcode', 
            'type' => 'Text', 
            'options' => array(
                'label' => 'Zip Code/Postal Code',
            ),
        ));
        $this->add(array(
            'name' => 'country', 
            'type' => 'Text', 
            'options' => array(
                'label' => 'Country',
            ),
        ));
        $this->add(array(
            'name' => 'phone', 
            'type' => 'Text', 
            'options' => array(
                'label' => 'Phone',
            ),
        ));
    }
}