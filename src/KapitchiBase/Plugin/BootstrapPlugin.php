<?php

namespace KapitchiBase\Plugin;

use Zend\EventManager\Event;

interface BootstrapPlugin {
    public function onBootstrap(Event $e);
}