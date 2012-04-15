<?php

namespace KapitchiBase\Module\Plugin;

use Zend\Module\Manager as ModuleManager,
    Zend\Mvc\AppContext as Application,
    Zend\EventManager\Event,
    KapitchiBase\Module\ModuleAbstract,
    KapitchiBase\Stdlib\Options;

abstract class PluginAbstract implements PluginInterface, BootstrapPlugin {
    protected $module;
    protected $options;
    protected $pluginName;
    
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

}