<?php
namespace Stuff\Filter;

use Stuff\Filter\AbstractRequestFilter;
use Zend\InputFilter\FileInput;

class BuyFilter extends AbstractRequestFilter {
    protected function getAddressFilter(){
        return array(
                'name' => 'address',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please enter address',
                        ),
                    ),
               )
            );
    }
    
    protected function getDescriptionFilter(){
        return array(
                'name' => 'description',
                'required' => false,
        );
    }
    
    protected function getPhoneFilter(){
        return array(
                'name' => 'phone',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please enter phone number',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Int',
                        'options' => array(
                            'message' => 'Phone must be a number'
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 8,
                            'max' => 11
                        )
                    )
                )
            );
    }

    protected function getPaymentMethodFilter(){
        return array(
            'name' => 'paymentmethod',
            'required' => true,
        );
    }
}
