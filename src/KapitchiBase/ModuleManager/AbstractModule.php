<?php

namespace KapitchiBase\ModuleManager;

use Zend\ModuleManager\Feature\BootstrapListenerInterface,
    Zend\EventManager\Event,
    Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\ModuleManager\Feature\LocatorRegisteredInterface;

abstract class AbstractModule
    implements BootstrapListenerInterface,
        AutoloaderProviderInterface, LocatorRegisteredInterface
{
    abstract public function getDir();
    abstract public function getNamespace();
    
    public function onBootstrap(Event $e) {
        //$app = $e->getParam('application');
        //$mergedConfig = $e->getParam('config');
    }
    
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                $this->getDir() . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    $this->getNamespace() => $this->getDir() . '/src/' . $this->getNamespace(),
                ),
            ),
        );
    }
    
    public function getConfig()
    {
        return include $this->getDir() . '/config/module.config.php';
    }
    
}