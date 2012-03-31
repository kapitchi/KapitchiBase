<?php

namespace KapitchiBase\Mapper;

use Zend\Stdlib\ArrayUtils,
    ZfcBase\Mapper\DbAdapterMapper,
    KapitchiBase\Stdlib\PluralField;

/**
 * TODO this has to be finished!!!!
 */
class PluralFieldObjectDbAdapter extends DbAdapterMapper {
    protected $tableName;
    protected $objectMapper;
    
    public function findByEntityId($entityId) {
        $pluralField = new PluralField();
        $table = $this->getTableGateway($this->tableName);
        $result = $table->select(array(
            'entityId' => $entityId
        ));
        
        $array = array();
        $objectMapper = $this->getObjectMapper();
        foreach($result as $item) {
            $objectId = $item->value;
            $object = $objectMapper->findByPriKey($objectId);
            $array[] = array(
                'type' => $item->type,
                'primary' => (bool)$item->primary,
                'value' => $object,
            );
        }
        $pluralField->exchangeArray($array);
        return $pluralField;
    }
    
    public function persist($entityId, PluralField $pluralField) {
        $table = $this->getTableGateway($this->tableName);
        $table->delete(array(
            'entityId' => $entityId
        ));
        
        foreach($pluralField as $item) {
            $model = $item->value;
            $ret = $this->getObjectMapper()->persist($model);
            $objectId = $model->getId();
            
            $table->insert(array(
                'entityId' => $entityId,
                'type' => $item->type,
                'primary' => $item->primary,
                'value' => $objectId,
            ));
        }
        
        return $pluralField;
    }
    
    public function getObjectMapper() {
        return $this->objectMapper;
    }

    public function setObjectMapper($objectMapper) {
        $this->objectMapper = $objectMapper;
    }

    public function getTableName() {
        return $this->tableName;
    }

    public function setTableName($tableName) {
        $this->tableName = $tableName;
    }

}