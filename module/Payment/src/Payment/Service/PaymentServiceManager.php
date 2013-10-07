<?php
namespace Payment\Service;

use Payment\Service\Cash;
use Payment\Service\Credit;

class PaymentServiceManager{
    public function get($classname){
        if($classname ==  "Cash"){
            return new Cash();
        }
        if($classname == "Credit"){
            return new Credit();
        }
    }
    
    public function listfile(){
        return scandir("./");
    }
}