<?php

namespace KapitchiBase\Module\Plugin;

use Zend\Mvc\AppContext as Application;

interface BootstrapPlugin {
    public function bootstrap(Application $e);
}