<?php

namespace KapitchiBase\Plugin;

use Zend\Module\Manager,
    Zend\EventManager\StaticEventManager,
    Zend\Mvc\AppContext as Application,
    ZfcBase\Model\ModelAbstract,
    ZfcBase\Form\Form;

abstract class ModelPlugin extends PluginAbstract {
    protected $modelServiceClass;
    protected $modelFormClass;
    protected $extName;
    
    abstract public function getModel(ModelAbstract $model);
    abstract public function getForm();
    abstract public function persistModel(ModelAbstract $model, array $extData, array $data);
    abstract public function removeModel(ModelAbstract $model);
    
    protected function bootstrap(Application $app) {
        $this->setApplication($app);
        
        $events = StaticEventManager::getInstance();
        
        $events->attach($this->getModelServiceClass(), 'persist.post', array($this, 'onModelPersistPost'));
        $events->attach($this->getModelServiceClass(), 'remove.post', array($this, 'onModelRemovePost'));
        $events->attach($this->getModelServiceClass(), array('get.ext.' . $this->getExtName(), 'get.exts'), array($this, 'onGetModel'));
        if($this->getModelFormClass()) {
            $events->attach($this->getModelFormClass(), 'construct.post', array($this, 'onCreateForm'));
        }
    }
    
    protected function createForm(Form $form) {
        $extForm = $this->getForm();
        if($extForm) {
            $form->addExtSubForm($extForm, $this->getExtName());
        }
    }
    
    public function onCreateForm($e) {
        $this->createForm($e->getTarget());
    }
    
    public function onModelPersistPost($e) {
        $data = $e->getParam('data');
        $model = $e->getParam('model');
        $extData = $data['exts'][$this->getExtName()];
        $extModel = $this->persistModel($model, $extData, $data);
        if($extModel) {
            $model->ext($this->getExtName(), $extModel);
        }
    }
    
    public function onModelRemovePost($e) {
        $data = $e->getParam('data');
        $model = $e->getParam('model');
        $extData = $data['exts'][$this->getExtName()];
        $extModel = $this->removeModel($model);
        if($extModel) {
            $model->ext($this->getExtName(), null);
        }
    }
    
    public function onGetModel($e) {
        $model = $e->getParam('model');
        $extModel = $this->getModel($model);
        if($extModel) {
            $model->ext($this->getExtName(), $extModel);
        }
    }
    
    public function getLocator() {
        return $this->getApplication()->getLocator();
    }
    
    public function getModuleManager() {
        return $this->moduleManager;
    }

    public function setModuleManager($moduleManager) {
        $this->moduleManager = $moduleManager;
    }
    
    public function getApplication() {
        return $this->application;
    }

    public function setApplication($application) {
        $this->application = $application;
    }

    public function getModelServiceClass() {
        return $this->modelServiceClass;
    }

    public function setModelServiceClass($modelServiceClass) {
        $this->modelServiceClass = $modelServiceClass;
    }

    public function getModelFormClass() {
        return $this->modelFormClass;
    }

    public function setModelFormClass($modelFormClass) {
        $this->modelFormClass = $modelFormClass;
    }

    public function getExtName() {
        return $this->extName;
    }

    public function setExtName($extName) {
        $this->extName = $extName;
    }

}