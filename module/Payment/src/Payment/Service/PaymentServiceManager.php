<?php
namespace Payment\Service;

use Payment\Service\Cash;
use Payment\Service\Credit;

class PaymentServiceManager{
    public function get($classname){
        $services = $this->getList();
        foreach ($services as $key => $value) {
            if($classname == $key){
                $class = "Payment\\Service\\". $key;
                return new $class;
            }
        }
    }
    
    public function getList(){
        $array =  scandir("./module/Payment/src/Payment/Service");
        $return = array();
        foreach($array as $value ){
            if($value != "." && $value != ".." 
            && $value != "PaymentServiceManager.php"
            && $value != "PaymentServiceInterface.php"){
                $name = substr($value, 0, strlen($value)-4);
                $return[$name] = $name;                
            }
        }
        return $return;
    }
}