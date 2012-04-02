<?php

namespace KapitchiBase\Crypt;

use ZfcBase\Util\String;

class Hash {
    protected $algorithm = 'md5';
    protected $sharedSalt = '';
    protected $cost = 0;
    
    public function encrypt($str)  {
        $algorithm = $this->getAlgorithm();
        $salt = $this->getSalt($algorithm);
        $saltedStr = $this->saltString($str);
        $hash = crypt($saltedStr, $salt);
        return $hash;
    }
    
    public function isEqual($str, $hash) {
        $saltedStr = $this->saltString($str);
        $crypted = crypt($saltedStr, $hash);
        return ($crypted == $hash);
    }
    
    protected function saltString($str) {
        return $this->getSharedSalt() . $str;
    }
    
    protected function getSalt($algorithm) {
        $cost = $this->getCost();
        switch(strtolower($algorithm)) {
            case 'md5':
                $salt = '$1$' . $this->getCryptSaltString(9);
                break;
            case 'blowfish':
                $cost = str_pad(($cost < 4 || $cost > 31) ? 10 : $cost, 2, '0', STR_PAD_LEFT);
                $salt = '$2a$' . $cost . '$' . $this->getCryptSaltString(22) . '$';
                break;
            case 'sha256':
                $cost = ($cost < 1000 || $cost > 999999999) ? 5000 : $cost;
                $salt = '$5$rounds=' . $cost . '$' . $this->getCryptSaltString(22) . '$';
                break;
            case 'sha512':
                $cost = ($cost < 1000 || $cost > 999999999) ? 5000 : $cost;
                $salt = '$6$rounds=' . $cost . '$' . $this->getCryptSaltString(22) . '$';
                break;
            default:
                throw new \InvalidArgumentException("Unupported algorithm $algorithm");
        }
        
        return $salt;
    }
    
    protected function getCryptSaltString($length) {
        return str_replace('+', '.', substr(base64_encode(String::getRandomBytes($length)), 0, $length));
    }
    
    //getters/setters
    public function getSharedSalt() {
        return $this->sharedSalt;
    }
    
    public function setSharedSalt($sharedSalt) {
        $this->sharedSalt = $sharedSalt;
    }
    
    public function getAlgorithm() {
        return $this->algorithm;
    }

    public function setAlgorithm($algorithm) {
        if(!in_array($algorithm, array(
            'md5', 'blowfish', 'sha256', 'sha512'
        ))) {
            throw new \InvalidArgumentException("Unsupported algorithm '$algorithm'");
        }
        $this->algorithm = $algorithm;
    }

    public function getCost() {
        return $this->cost;
    }

    public function setCost($cost) {
        $this->cost = $cost;
    }


}