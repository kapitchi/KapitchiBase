<?php

namespace KapitchiBase\Module\Plugin;

use Zend\EventManager\SharedEventManagerAwareInterface,
    Zend\EventManager\SharedEventManagerInterface,
    KapitchiBase\Stdlib\Options;

abstract class PluginAbstract implements PluginInterface, BootstrapPlugin, SharedEventManagerAwareInterface {
    protected $module;
    protected $options;
    protected $pluginName;
    protected $sharedEventManager;
    
    public function getOption($option, $default = null) {
        return $this->getOptions()->get($option, $default);
    }
    
    //getters/setters

    public function getModule() {
        return $this->module;
    }

    public function setModule($module) {
        $this->module = $module;
    }

    public function getPluginName() {
        return $this->pluginName;
    }

    public function setPluginName($pluginName) {
        $this->pluginName = $pluginName;
    }
    
    public function getOptions() {
        if($this->options === null) {
            $this->options = new Options();
        }
        return $this->options;
    }

    public function setOptions($options) {
        if(!$options instanceof Options) {
            $options = new Options($options);
        }
        $this->options = $options;
    }

    /**
     * Inject a SharedEventManager instance
     * 
     * @param  SharedEventManagerInterface $sharedEventManager
     * @return SharedEventManagerAwareInterface
     */
    public function setSharedManager(SharedEventManagerInterface $sharedEventManager) {
        $this->sharedEventManager = $sharedEventManager;
    }

    /**
     * Get shared collections container
     *
     * @return SharedEventManagerInterface
     */
    public function getSharedManager() {
        return $this->sharedEventManager;
    }

    /**
     * Remove any shared collections
     *
     * @return void
     */
    public function unsetSharedManager() {
        $this->sharedEventManager = null;
    }
}