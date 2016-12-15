<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Adteam\Core\Checkout\Validator;

/**
 * Description of Cedis
 *
 * @author dev
 */
class Cedis {
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
        if($this->config==='cedis'){
            $this->isExist();
            $isValid = true;
        }
        
        if($this->config==='none'||$this->config==='user-address'){
            $isValid = true;
        }
        
        return $isValid;
    }
    
    private function isExist()
    {
        if(!isset($this->data->cedis)){
               throw new \InvalidArgumentException(
                    'Required_Cedis'); 
        }
    }
}
