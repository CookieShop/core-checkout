<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Adteam\Core\Checkout\Validator;

/**
 * Description of UserAddress
 *
 * @author dev
 */
class UserAddress {

    protected $config;
    
    protected $data;
    
    protected $editable;
    
    public function __construct($data,$configs) {
         $this->config = $configs['checkout.delivery.mode'];
         $this->editable = $configs['checkout.delivery.userEditable'];
         $this->data = $data;
    }    
    
    public function isValid()
    {
       $isValid = false;
        if($this->config==='user-address'&&$this->editable==='editable'){
            $this->isExistAddress();
            $isValid = true;
        }
        
        if($this->config==='cedis'&&$this->editable==='editable'){
            $isValid = true;
        }
        
        return $isValid;
    }
    
    private function isExistAddress()
    {
        if(!isset($this->data->userAddress)){
               throw new \InvalidArgumentException(
                    'Address_Is_Required'); 
        }
    }
    
    private function isExistCedis()
    {
        if(!isset($this->data->cedis)){
               throw new \InvalidArgumentException(
                    'Cedis_Is_Required'); 
        }        
    }
}
