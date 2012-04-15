<?php
namespace KapitchiBase\Module\Plugin;

interface PluginInterface {
    public function setOptions($options);
    public function getOptions();
    public function setPluginName($name);
    public function getPluginName();
}