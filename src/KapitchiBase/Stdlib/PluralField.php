<?php
/**
 * Kapitchi Zend Framework 2 Modules (http://kapitchi.com/)
 *
 * @copyright Copyright (c) 2012-2013 Kapitchi Open Source Team (http://kapitchi.com/open-source-team)
 * @license   http://opensource.org/licenses/LGPL-3.0 LGPL 3.0
 */

namespace KapitchiBase\Stdlib;

use ArrayObject;

class PluralField extends ArrayObject {
    
    public static function fromArray(array $data) {
        $pluralField = new self();
        $pluralField->exchangeArray($data);
        return $pluralField;
    }
    
    public function exchangeArray($data) {
        foreach($data as $type => $values) {
            if(!array_key_exists('value', $values)) {
                throw new \RuntimeException('value item is missing');
            }
            $value = $values['value'];
            
            if(!empty($values['type'])) {
                $type = $values['type'];
            }
            
            $primary = false;
            if(!empty($values['primary'])) {
                $primary = true;
            }
            
            $object = new ArrayObject(array(
                'type' => $type,
                'value' => $value,
                'primary' => $primary,
            ), ArrayObject::ARRAY_AS_PROPS);
            
            $this->append($object);
        }
    }
    
    public function toArray() {
        $array = array();
        foreach($this as $item) {
            $value = $item->value;
            if(is_object($value)) {
                if(is_callable(array($value, 'toArray'))) {
                    $value = $value->toArray();
                }
            }
            
            $array[$item->type] = array(
                'type' => $item->type,
                'primary' => $item->primary,
                'value' => $value,
            );
        }
        
        return $array;
    }
    
    public function getByType($type) {
        foreach($this as $item) {
            if($item->type == $type) {
                return $item;
            }
        }
    }
    
    public function getPrimary() {
        foreach($this as $item) {
            if($item->primary) {
                return $item;
            }
        }
        
        return null;
    }
    
}