<?php

namespace KapitchiBase\Mapper;

use Zend\Db\Adapter\Adapter,
        Zend\Db\Adapter\AdapterAwareInterface,
        Zend\Db\TableGateway\TableGateway;

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
    
    public function beginTransaction($write = true) {
        
    }
    
    public function commit($write = true) {
        
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