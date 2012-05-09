<?php

namespace KapitchiBase\Acl;

class ModelEventGuardDef extends \ZfcAcl\Model\EventGuardDefTriggeredEventAware {
    public function getResource() {
        $event = $this->getTriggeredEvent();
        return $event->getParam('model');
    }
}