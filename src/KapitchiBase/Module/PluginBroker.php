<?php

namespace KapitchiBase\Module;

use Zend\Loader\PluginSpecBroker,
    Zend\Mvc\ApplicationInterface,
    Zend\Stdlib\ArrayUtils,
    KapitchiBase\Module\Plugin\BootstrapPlugin,
    RuntimeException as InvalidPluginException;

class PluginBroker extends PluginSpecBroker {
    protected $module;
    protected $bootstrapPlugins = array();
    
    public function __construct(ModuleAbstract $module) {
        $this->setModule($module);
    }
    
    public function onBootstrap(Event $e) {
        foreach($this->getBootstrapPlugins() as $pluginName) {
            $plugin = $this->load($pluginName);
            $plugin->onBootstrap($e);
        }
    }
    
    /**
     * @param mixed $plugin 
     * @param array $options 
     * @return void
     */
    public function load($plugin, array $options = null)
    {
        $instance = parent::load($plugin, $options);
        $instance->setPluginName($plugin);
        $instance->setModule($this->getModule());
        return $instance;
    }
    
    /**
     * @param  mixed $plugin 
     * @return true
     * @throws InvalidPluginException
     */
    protected function validatePlugin($plugin)
    {
        if (!$plugin instanceof BootstrapPlugin) {
            throw new InvalidPluginException('Invalid module plugin');
        }
        return true;
    }
    
    /**
     * Accepts two types of array:
     * 1. array('Plugin1', 'Plugin2', ...)
     * 2. array('Plugin1' => false, 'Plugin2' => true, ...) - while Plugin1 will be filtered out
     * 
     * @param array $plugins 
     */
    public function setBootstrapPlugins(array $plugins) {
        if(ArrayUtils::hasStringKeys($plugins)) {
            $plugins = array_filter($plugins);
            $plugins = array_keys($plugins);
        }
        
        $this->bootstrapPlugins = $plugins;
    }
    
    public function isPluginBootstraped($pluginName) {
        return in_array($pluginName, $this->getBootstrapPlugins());
    }
    
    public function getBootstrapPlugins() {
        return $this->bootstrapPlugins;
    }
    
    public function setOptions($options)
    {
        parent::setOptions($options);

        foreach ($options as $key => $value) {
            switch(strtolower($key)) {
                case 'bootstrap_plugins':
                    if (!is_array($value) && !$value instanceof \Traversable) {
                        throw new \RuntimeException(sprintf(
                            'Expected array or Traversable for bootstrap_plugins option; received "%s"',
                            (is_object($value) ? get_class($value) : gettype($value))
                        ));
                    }
                    
                    $this->setBootstrapPlugins($value);
                    
                    break;
                default:
                    // ignore unknown options
                    break;
            }
        }

        return $this;
    }
    
    public function getModule() {
        return $this->module;
    }

    public function setModule($module) {
        $this->module = $module;
    }

}