<?php

namespace KapitchiBase\Acl;

use Zend\Mvc\AppContext as Application;

class ModelEventGuardDef extends \ZfcAcl\Model\EventGuardDefTriggeredEventAware {
    public function getResource() {
        $event = $this->getTriggeredEvent();
        return $event->getParam('model');
    }
}