<?php

namespace KapitchiBase\Mapper;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\AdapterAwareInterface,
    Zend\Db\TableGateway\TableGateway,
    InvalidArgumentException as CannotConvertToScalarException,
    InvalidArgumentException as NotArrayException;

abstract class DbAdapterMapper implements Transactional, AdapterAwareInterface {
    
    /**
     * Database adapter for read queries
     *
     * @var Zend\Db\Adapter\Adapter
     */
    protected $readAdapter;

    /**
     * Database adapter for write queries
     *
     * @var Zend\Db\Adapter\Adapter
     */
    protected $writeAdapter;
    
    
    /**
     * @param type $tableName
     * @param type $write
     * @return Zend\Db\TableGateway\TableGateway 
     */
    protected function getTableGateway($tableName, $write = false) {
        $adapter = $write ? $this->getWriteAdapter() : $this->getReadAdapter();
        $tableGateway = new TableGateway($tableName, $adapter);
        
        return $tableGateway;
    }
    
    protected function toScalarValueArray($values) {
        //convert object toArray first
        if(is_object($values)) {
            if(is_callable(array($values, 'toScalarValueArray'))) {
                return $values->toScalarValueArray();
            }
            
            if(is_callable(array($values, 'toArray'))) {
                $values = $values->toArray();
            }
        }
        
        if(!is_array($values)) {
            throw new NotArrayException("Parameter is not an array");
        }
        
        $ret = array();
        foreach($values as $key => $value) {
            if(is_scalar($value)) {
                $ret[$key] = $value;
                continue;
            }
            if(is_object($value)) {
                $ret[$key] = $this->convertObjectToScalar($value);
                continue;
            }
            if($value == null) {
                $ret[$key] = null;
                continue;
            }
            
            throw new CannotConvertToScalarException("Can not convert '$key' key value to string");
        }
        
        return $ret;
    }
    
    protected function convertObjectToScalar($obj) {
        //TODO XXX we don't want to create sub arrays here! that's why this functionality should not be here and is commented out for now.
        
        //does object support toScalarValueArray?
        //if(is_callable(array($obj, 'toScalarValueArray'))) {
             //return $obj->toScalarValueArray();
        //}
        //... if not, does it support toArray? if it does we try to convert it using this mapper.
        //if(is_callable(array($obj, 'toArray'))) {
             //return $this->toScalarValueArray($obj->toArray());
        //}
        
        //END
        
        if(is_callable(array($obj, '__toString'))) {
            return $obj->__toString();
        }
        if($obj instanceof \DateTime) {
            return $obj->format('Y-m-d\TH:i:sP');
        }
        
        throw new CannotConvertToScalarException("Can not convert object '" . get_class($obj) . "' to string");
    }
    
    public function beginTransaction($write = true) {
        
    }
    
    public function commit($write = true) {
        
    }
    
    public function rollback($write = true) {
        
    }
    
    public function setDbAdapter(Adapter $adapter) {
        $this->setReadAdapter($adapter);
        $this->setWriteAdapter($adapter);
    }
    
    public function setReadAdapter(Adapter $readAdapter) {
        $this->readAdapter = $readAdapter;
    }
    
    public function getReadAdapter() {
        return $this->readAdapter;
    }
    
    public function getWriteAdapter() {
        return $this->writeAdapter;
    }

    public function setWriteAdapter(Adapter $writeAdapter) {
        $this->writeAdapter = $writeAdapter;
    }
}