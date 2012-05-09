<?php

namespace KapitchiBase\Module\Plugin;

use Zend\Mvc\ApplicationInterface;

interface BootstrapPlugin {
    public function bootstrap(ApplicationInterface $app);
}