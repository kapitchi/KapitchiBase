<?php
/**
 * Originally copied from:
 * https://github.com/ZF-Commons/ZfcBase/blob/master/src/ZfcBase/Model/ModelAbstract.php
 * 
 * This implements RowObjectInterface so it can be used with db table gateways also
 */

namespace KapitchiBase\Model;

use Zend\Stdlib\ArrayUtils,
    DateTime;

abstract class ModelAbstract implements \Zend\Db\ResultSet\RowObjectInterface
{
    protected $exts = array();
    const ARRAYSET_PRESERVE_KEYS    = 0;
    const ARRAYSET_RESET_KEYS       = 1;

    /**
     * Convert an array to an instance of a model class
     *
     * @param array $array
     * @return ZfcBase\Model\ModelAbstract
     */
    public static function fromArray($array)
    {
        if (!ArrayUtils::hasStringKeys($array)) {
            throw new \InvalidArgumentException('KapitchiBase\Model::fromArray expects associative array.');
        }
        $model = new static();
        $model->exchangeArray($array);
        return $model;
    }

    /**
     * Convert an array of arrays into an array of model classes
     *
     * @param array $array
     * @param int $mode
     * @return array
     */
    public static function fromArraySet(array $array, $mode = self::ARRAYSET_PRESERVE_KEYS)
    {
        $return = array();
        foreach ($array as $key => $value) {
            if ($mode === self::ARRAYSET_PRESERVE_KEYS) {
                $return[$key] = static::fromArray($value);
            } else if ($mode == self::ARRAYSET_RESET_KEYS) {
                $return[] = static::fromArray($value);
            }
        }
        return $return;
    }

    public function ext($extension, $value = null)
    {
        if (null !== $value) {
            $this->exts[$extension] = $value;
        }
        if (!isset($this->exts[$extension])) {
            return null;
        }
        return $this->exts[$extension];
    }

    public function exchangeArray($array) {
        foreach ($array as $key => $value) {
            $setter = static::fieldToSetterMethod($key);
            if (is_callable(array($this, $setter))) {
                $this->$setter($value);
            }
        }
    }
    
    /**
     * Convert a model class to an array recursively
     *
     * @param mixed $array
     * @return array
     */
    public function toArray($array = false)
    {
        $array = $array ?: get_object_vars($this);
        foreach ($array as $key => $value) {
            unset($array[$key]);
            //matusz: commented out - do we really need this??
            //$key = static::fromCamelCase($key);
            $getter = static::fieldToGetterMethod($key);
            if (is_callable(array($this, $getter))) {
                $value = $this->$getter();
            }
            if (is_object($value)) {
                if (is_callable(array($value, 'toArray'))) {
                    $array[$key] = $value->toArray();
                } elseif ($value instanceof DateTime) {
                    $array[$key] = $value->format('Y-m-d H:i:s'); // meh...
                } else {
                    $array[$key] = $value;
                }
            } elseif (is_array($value) && count($value) > 0) {
                $array[$key] = $this->toArray($value);
            } elseif ($value !== NULL && !is_array($value)) {
                $array[$key] = $value;
            }
        }
        return $array;
    }
    
    public function count() {
        $vars = get_object_vars($this);
        unset($vars['exts']);
        return count($vars);
    }
    
    public function offsetExists($key) {
        $getter = self::fieldToGetterMethod($key);
        if(is_callable(array($this, $getter))) {
            return true;
        }
        
        return false;
    }

    public function offsetGet($key) {
        $getter = self::fieldToGetterMethod($key);
        if(is_callable(array($this, $getter))) {
            return $this->$getter();
        }
        
        return null;
    }

    public function offsetSet($key, $value) {
        throw new \Exception("offsetSet n/i");
    }
    
    public function offsetUnset($key) {
        throw new \Exception("offsetUnset n/i");
    }
    
    public static function fieldToSetterMethod($name)
    {
        return 'set' . static::toCamelCase($name);
    }

    public static function fieldToGetterMethod($name)
    {
        return 'get' . static::toCamelCase($name);
    }

    public static function toCamelCase($name)
    {
        return implode('',array_map('ucfirst', explode('_',$name)));
    }

    public static function fromCamelCase($name)
    {
        return trim(preg_replace_callback('/([A-Z])/', function($c){ return '_'.strtolower($c[1]); }, $name),'_');
    }
}
