<?php
namespace Payment\Service;

use Payment\Service\PaymentServiceInterface;

class Credit implements PaymentServiceInteface{
    private $error = array();
    
    protected $form;
    
    public function pay($data = array()){
        if(count($data) <= 3){
            array_push($error,"Not enough information to process");
            return false;
        }
        if(strlen($data['cardNumber']) < 16){
            array_push($error,"Card number is not valid");
            return false;
        }
        
        return true;
    }    
    
    public function getMessage(){
        return $error;
    }
    
    public function getForm()
    {
        return $this->form;
    }
}