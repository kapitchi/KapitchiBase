<?php

namespace KapitchiBase\Module;

use Zend\ModuleManager\Feature\BootstrapListenerInterface,
    Zend\EventManager\Event,
    Zend\Loader\Pluggable as PluggableInterface,
    ZfcBase\Module\AbstractModule as ZfcModuleAbstract,
    KapitchiBase\Module\PluginBroker;

abstract class ModuleAbstract extends ZfcModuleAbstract implements PluggableInterface, BootstrapListenerInterface {
    
    protected $broker;
    
    public function onBootstrap(Event $e) {
        $app = $e->getParam('application');
        $mergedConfig = $e->getParam('config');
        $this->setMergedConfig($mergedConfig);

        $sm = $app->getServiceManager();
        $broker = $this->getBroker();
        $broker->setServiceLocator($sm);

        if(isset($mergedConfig[$this->getNamespace()]['plugin_broker'])) {
            $brokerOptions = $mergedConfig[$this->getNamespace()]['plugin_broker'];

            $broker->setOptions($brokerOptions->toArray());
            $broker->onBootstrap($app);
        }
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