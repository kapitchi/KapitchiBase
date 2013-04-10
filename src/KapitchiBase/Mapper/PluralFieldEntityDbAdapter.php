<?php
/**
 * Kapitchi Zend Framework 2 Modules (http://kapitchi.com/)
 *
 * @copyright Copyright (c) 2012-2013 Kapitchi Open Source Team (http://kapitchi.com/open-source-team)
 * @license   http://opensource.org/licenses/LGPL-3.0 LGPL 3.0
 */

namespace KapitchiBase\Mapper;

use KapitchiBase\Mapper\DbAdapterMapper,
    KapitchiBase\Stdlib\PluralField;

/**
 * TODO - UNFINISHED!
 */
class PluralFieldEntityDbAdapter extends DbAdapterMapper {
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