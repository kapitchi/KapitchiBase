<?php

namespace KapitchiBase\Plugin;

use Zend\Module\Manager as ModuleManager,
    Zend\Mvc\AppContext as Application,
    Zend\EventManager\Event,
    KapitchiBase\Module\ModuleAbstract;

abstract class PluginAbstract implements BootstrapPlugin {
    protected $moduleManager;
    protected $module;
    protected $pluginName;
    
    abstract protected function bootstrap(Application $application);
    
    public function __construct($pluginName, ModuleAbstract $module, ModuleManager $moduleManager) {
        $this->setPluginName($pluginName);
        $this->setModule($module);
        $this->setModuleManager($moduleManager);
    }
    
    public function onBootstrap(Event $e) {
        $application = $e->getParam('application');
        $this->bootstrap($application);
    }
    
    public function getOption($option, $default = null) {
        return $this->getModule()->getOption('plugins.' . $this->getPluginName() . '.options.' . $option, $default);
    }
    
    //getters/setters
    public function getModuleManager() {
        return $this->moduleManager;
    }

    public function setModuleManager($moduleManager) {
        $this->moduleManager = $moduleManager;
    }

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

}