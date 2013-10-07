<?php
namespace Payment\Service;

use Payment\Service\PaymentServiceInterface;

class Credit implements PaymentServiceInterface{
    private $error = array();
    
    protected $form;
    
    public function pay($data = array()){
        
        if(!isset($data)){
            array_push($this->error,"Not enough information to process");
            return false;
        }
        if(isset($data['cardNumber'])){
            array_push($this->error,"Card number is not valid");
            return false;
        }
        
        return true;
    }    
    
    public function getMessage(){
        return $this->error;
    }
    
    public function getForm()
    {
        return $this->form;
    }
}