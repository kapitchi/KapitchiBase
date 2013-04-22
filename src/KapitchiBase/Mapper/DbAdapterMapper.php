<?php
/**
 * Kapitchi Zend Framework 2 Modules (http://kapitchi.com/)
 *
 * @copyright Copyright (c) 2012-2013 Kapitchi Open Source Team (http://kapitchi.com/open-source-team)
 * @license   http://opensource.org/licenses/LGPL-3.0 LGPL 3.0
 */

namespace KapitchiBase\Mapper;

use Zend\Db\Adapter\Adapter,
    Zend\Db\Adapter\AdapterAwareInterface,
    Zend\Db\TableGateway\TableGateway;

class DbAdapterMapper implements TransactionalInterface, AdapterAwareInterface
{
    /**
     * @var array
     */
    private static $transactionCount = array();
    
    /**
     * Database adapter for read queries
     *
     * @var Zend\Db\Adapter\Adapter
     */
    protected $readDbAdapter;

    /**
     * Database adapter for write queries
     *
     * @var Zend\Db\Adapter\Adapter
     */
    protected $writeDbAdapter;
    
    private $tableGateways = array();

    public function __construct(Adapter $dbAdapter)
    {
        $this->setDbAdapter($dbAdapter);
    }
    /**
     * @param string $tableName
     * @param bool $write
     * @return \Zend\Db\TableGateway\TableGateway 
     */
    public function getTableGateway($tableName, $write = false)
    {
        $typeStr = $write ? 'write' : 'read';
        
        //checks for existing instance
        if (isset($this->tableGateways[$typeStr][$tableName])) {
            return $this->tableGateways[$typeStr][$tableName];
        }
        
        $adapter = $write ? $this->getWriteDbAdapter() : $this->getReadDbAdapter();
        $tableGateway = new TableGateway($tableName, $adapter);
        
        //keep the instance
        $this->tableGateways[$typeStr][$tableName] = $tableGateway;
        
        return $tableGateway;
    }
    
    public function beginTransaction()
    {
        $this->performTransactionOperation('beginTransaction', $this->getWriteDbAdapter());
    }
    
    public function commit()
    {
        $this->performTransactionOperation('commit', $this->getWriteDbAdapter());
    }
    
    public function rollback()
    {
        $this->performTransactionOperation('rollback', $this->getWriteDbAdapter());
    }
    
    private function performTransactionOperation($operation, Adapter $adapter) 
    {
        $adapterHash = spl_object_hash($adapter);
        if (!isset(self::$transactionCount[$adapterHash])) {
            self::$transactionCount[$adapterHash] = 0;
        }
        
        switch ($operation) {
            case 'beginTransaction':
                if (self::$transactionCount[$adapterHash] == 0) {
                    $adapter->getDriver()->getConnection()->beginTransaction();
                }
                self::$transactionCount[$adapterHash]++;
                break;
            case 'commit':
                if (self::$transactionCount[$adapterHash] == 1) {
                    $adapter->getDriver()->getConnection()->commit();
                }
                self::$transactionCount[$adapterHash]--;
                break;
            case 'rollback':
                if (self::$transactionCount[$adapterHash] == 1) {
                    $adapter->getDriver()->getConnection()->rollback();
                }
                self::$transactionCount[$adapterHash]--;
                break;
        }
    }
    
    public function setDbAdapter(Adapter $adapter)
    {
        $this->setReadDbAdapter($adapter);
        $this->setWriteDbAdapter($adapter);
    }
    
    // getters/setters
    /**
     * 
     * @return \Zend\Db\Adapter\Adapter
     */
    public function getReadDbAdapter()
    {
        return $this->readDbAdapter;
    }

    public function setReadDbAdapter(Adapter $readDbAdapter)
    {
        $this->readDbAdapter = $readDbAdapter;
    }

    public function getWriteDbAdapter()
    {
        return $this->writeDbAdapter;
    }

    public function setWriteDbAdapter(Adapter $writeDbAdapter)
    {
        $this->writeDbAdapter = $writeDbAdapter;
    }

}
