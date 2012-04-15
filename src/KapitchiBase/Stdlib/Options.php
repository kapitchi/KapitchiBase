<?php
namespace KapitchiBase\Stdlib;

use Traversable,
    Zend\Stdlib\Options as ZendOptions,
    InvalidArgumentException as OptionNotAvaliable,
    ZfcBase\Util\String,
    Zend\Stdlib\Exception\BadMethodCallException;

/**
 * This implements Zend Options in convenient way so you don't have to implement getters/setters for each option
 */
class Options extends ZendOptions
{
    protected $options = array();
    
    public function get($option, $default = null)
    {
        try {
            $getter = $this->assembleGetterNameFromKey($option);
            $val = $this->$getter();
        } catch (BadMethodCallException $e) {
            $val = isset($this->options[$option]) ? $this->options[$option] : null;
        }
        
        if ($val === null) {
            if ($default === null) {
                throw new OptionNotAvaliable("Option '$option' not available");
            }
            
            return $default;
        }
        
        return $val;
    }
    
    public function __set($option, $value)
    {
        try {
            $setter = $this->assembleSetterNameFromKey($option);
            $this->$setter($value);
        } catch(BadMethodCallException $e) {
            $this->options[$option] = $value;
        }
    }
    
    public function __call($methodName, $args)
    {
        $type = substr($methodName, 0, 3);
        $camelOption = substr($methodName, 3);
        if ($type == 'get') {
            $option = String::fromCamelCase($camelOption);
            return $this->get($option);
        }
        elseif ($type == 'set') {
            $option = String::fromCamelCase($camelOption);
            if (count($args) != 1) {
                throw new \InvalidArgumentException("Invalid arguments for setter");
            }
            return $this->set($option, $args[0]);
        }
        
        throw new \Exception("'$methodName' is neither getter nor setter");
    }
}
