<?php
/**
 * Kapitchi Zend Framework 2 Modules (http://kapitchi.com/)
 *
 * @copyright Copyright (c) 2012-2013 Kapitchi Open Source Team (http://kapitchi.com/open-source-team)
 * @license   http://opensource.org/licenses/LGPL-3.0 LGPL 3.0
 */

namespace KapitchiBase\ModuleManager;

use Zend\ModuleManager\Feature\BootstrapListenerInterface,
    Zend\EventManager\EventInterface,
    Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\ModuleManager\Feature\LocatorRegisteredInterface;

abstract class AbstractModule
    implements BootstrapListenerInterface,
        AutoloaderProviderInterface, LocatorRegisteredInterface
{
    abstract public function getDir();
    abstract public function getNamespace();
    
    public function onBootstrap(EventInterface $e)
    {
        
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