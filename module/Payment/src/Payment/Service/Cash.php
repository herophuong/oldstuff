<?php
namespace Payment\Service;

use Payment\Service\PaymentServiceInterface;

class Cash implements PaymentServiceInterface{
    var $error;
    public function pay($data = array()){
        return true;      
    }
    
    public function getMessage(){
    
    }
}
