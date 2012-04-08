<?php

namespace KapitchiBase\Module;

use Zend\Module\Manager,
    Zend\Mvc\AppContext as Application,
    Zend\EventManager\StaticEventManager,
    Zend\Module\Consumer\AutoloaderProvider,
    Zend\Module\Consumer\LocatorRegistered,
    KapitchiIdentity\Form\Identity as IdentityForm,
    Zend\EventManager\EventDescription as Event,
    KapitchiBase\Plugin\BootstrapPlugin,
    RuntimeException as NoBootstrapPluginException;

abstract class ModuleAbstract extends \ZfcBase\Module\ModuleAbstract {
    
    public function init(Manager $moduleManager)
    {
        $events = StaticEventManager::getInstance();
        $instance = $this;//TODO this will no be needed in PHP 5.4
        $events->attach('bootstrap', 'bootstrap', function($e) use ($instance, $moduleManager) {
            $app = $e->getParam('application');
            $mergedConfig = $e->getParam('config');
            $instance->setMergedConfig($mergedConfig);
            $instance->bootstrap($moduleManager, $app);
            $locator = $app->getLocator();
            if(isset($mergedConfig[$instance->getNamespace()]['plugins'])) {
                $plugins = $mergedConfig[$instance->getNamespace()]['plugins'];
                
                foreach($plugins as $pluginName => $options) {
                    
                    //matuszemi: we enable plugins by default but if 'enabled' is set to false we switch them off!
                    if(isset($options['enabled']) && $options['enabled'] === false) {
                        continue;
                    }
                    
                    $plugin = $locator->get($options['diclass'], array(
                        'pluginName' => $pluginName,
                        'module' => $instance,
                        'moduleManager' => $moduleManager
                    ));
                    
                    if(!$plugin instanceof BootstrapPlugin) {
                        throw new NoBootstrapPluginException("Plugin '$pluginName' is not a bootstrap plugin");
                    }

                    $plugin->onBootstrap($e);
                }
            }
            
        });
        
    }
    
}