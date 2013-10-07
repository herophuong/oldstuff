<?php
namespace Payment\Service;

interface PaymentServiceInterface{
    public function pay($data = array());
    public function getMessage();
}
