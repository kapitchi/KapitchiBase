<?php
/**
 * Kapitchi Zend Framework 2 Modules (http://kapitchi.com/)
 *
 * @copyright Copyright (c) 2012-2013 Kapitchi Open Source Team (http://kapitchi.com/open-source-team)
 * @license   http://opensource.org/licenses/LGPL-3.0 LGPL 3.0
 */

namespace KapitchiBase\Mapper;

use Zend\Stdlib\ArrayUtils,
    KapitchiBase\Mapper\DbAdapterMapper,
    KapitchiBase\Stdlib\PluralField;

class PluralFieldStringDbAdapter extends DbAdapterMapper {
    protected $tableName;
    
    public function findByEntityId($entityId) {
        $pluralField = new PluralField();
        $table = $this->getTableGateway($this->tableName);
        $result = $table->select(array(
            'entityId' => $entityId
        ));
        
        $ret = ArrayUtils::iteratorToArray($result);
        $pluralField->exchangeArray($ret);
        return $pluralField;
    }
    
    public function persist($entityId, PluralField $pluralField) {
        $table = $this->getTableGateway($this->tableName);
        $table->delete(array(
            'entityId' => $entityId
        ));
        
        foreach($pluralField as $item) {
            $table->insert(array(
                'entityId' => $entityId,
                'type' => $item->type,
                'primary' => $item->primary,
                'value' => $item->value,
            ));
        }
        
        return $pluralField;
    }
    
    public function getTableName() {
        return $this->tableName;
    }

    public function setTableName($tableName) {
        $this->tableName = $tableName;
    }

}