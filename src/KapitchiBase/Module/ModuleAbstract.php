<?php

namespace KapitchiBase\Module;

use Zend\Module\Manager,
    Zend\Mvc\AppContext as Application,
    Zend\EventManager\StaticEventManager,
    Zend\Module\Consumer\AutoloaderProvider,
    Zend\Module\Consumer\LocatorRegistered,
    KapitchiIdentity\Form\Identity as IdentityForm,
    Zend\EventManager\EventDescription as Event,
    KapitchiBase\Module\Plugin\BootstrapPlugin,
    KapitchiBase\Module\PluginBroker,
    RuntimeException as NoBootstrapPluginException;

abstract class ModuleAbstract extends \ZfcBase\Module\ModuleAbstract implements \Zend\Loader\Pluggable {
    
    protected $broker;
    
    public function init(Manager $moduleManager)
    {
        $events = StaticEventManager::getInstance();
        $instance = $this;//TODO this will no be needed in PHP 5.4
        $events->attach('bootstrap', 'bootstrap', function($e) use ($instance, $moduleManager) {
            $app = $e->getParam('application');
            $mergedConfig = $e->getParam('config');
            $instance->setMergedConfig($mergedConfig);
            
            $locator = $app->getLocator();
            
            $broker = $instance->getBroker();
            $broker->setLocator($locator);
            
            $instance->bootstrap($moduleManager, $app);
            
            if(isset($mergedConfig[$instance->getNamespace()]['plugin_broker'])) {
                $brokerOptions = $mergedConfig[$instance->getNamespace()]['plugin_broker'];
                
                $broker->setOptions($brokerOptions->toArray());
                $broker->bootstrap($app);
            }
            
        });
        
    }
    
    /**
     * Get plugin broker instance
     * 
     * @return Zend\Loader\Broker
     */
    public function getBroker() {
        if($this->broker === null) {
            $broker = new PluginBroker($this);
            $broker->setClassLoader(new \Zend\Loader\PrefixPathLoader(array(
                $this->getNamespace() . '\Plugin' => $this->getDir() . '/src/' . $this->getNamespace() . '/Plugin'
            )));
            
            $this->broker = $broker;
        }
        
        return $this->broker;
    }

    /**
     * Set plugin broker instance
     * 
     * @param  string|Broker $broker Plugin broker to load plugins
     * @return Zend\Loader\Pluggable
     */
    public function setBroker($broker) {
        $this->broker = $broker;
    }

    /**
     * Get plugin instance
     * 
     * @param  string     $plugin  Name of plugin to return
     * @param  null|array $options Options to pass to plugin constructor (if not already instantiated)
     * @return mixed
     */
    public function plugin($name, array $options = null) {
        return $this->getBroker()->load($name, $options);
    }
}