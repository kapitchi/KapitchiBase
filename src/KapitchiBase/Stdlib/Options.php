<?php
/**
 * Kapitchi Zend Framework 2 Modules (http://kapitchi.com/)
 *
 * @copyright Copyright (c) 2012-2013 Kapitchi Open Source Team (http://kapitchi.com/open-source-team)
 * @license   http://opensource.org/licenses/LGPL-3.0 LGPL 3.0
 */

namespace KapitchiBase\Stdlib;

use Zend\Stdlib\Options as ZendOptions,
    InvalidArgumentException as OptionNotAvaliable,
    KapitchiBase\Stdlib\StringUtils,
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
            $option = StringUtils::fromCamelCase($camelOption);
            return $this->get($option);
        }
        elseif ($type == 'set') {
            $option = StringUtils::fromCamelCase($camelOption);
            if (count($args) != 1) {
                throw new \InvalidArgumentException("Invalid arguments for setter");
            }
            return $this->set($option, $args[0]);
        }
        
        throw new \Exception("'$methodName' is neither getter nor setter");
    }
}
