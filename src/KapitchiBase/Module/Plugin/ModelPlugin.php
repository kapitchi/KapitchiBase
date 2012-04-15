<?php

namespace KapitchiBase\Module\Plugin;

use Zend\Module\Manager,
    Zend\EventManager\StaticEventManager,
    Zend\Mvc\AppContext as Application,
    ZfcBase\Model\ModelAbstract,
    ZfcBase\Form\Form,
    ZfcBase\Service\Exception\ModelNotFoundException;

abstract class ModelPlugin extends PluginAbstract {
    protected $modelServiceClass;
    protected $modelFormClass;
    protected $priority = 1;
    protected $extName;
    
    abstract public function getModel(ModelAbstract $model);
    abstract public function persistModel(ModelAbstract $model, array $data, $extData);
    abstract public function removeModel(ModelAbstract $model);
    
    public function getForm() {
        
    }
    
    public function bootstrap(Application $app) {
        $this->setApplication($app);
        
        $events = StaticEventManager::getInstance();
        
        $events->attach($this->getModelServiceClass(), 'persist.post', array($this, 'onModelPersistPost'), $this->priority);
        $events->attach($this->getModelServiceClass(), 'remove.post', array($this, 'onModelRemovePost'), $this->priority);
        $events->attach($this->getModelServiceClass(), 'get.ext.' . $this->getExtName(), array($this, 'onGetExtModel'), $this->priority);
        $events->attach($this->getModelServiceClass(), 'get.exts', array($this, 'onGetExtsModel'), $this->priority);
        if($this->getModelFormClass()) {
            $events->attach($this->getModelFormClass(), 'construct.post', array($this, 'onCreateForm'), $this->priority);
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
        
        $extData = null;
        if(isset($data['exts'][$this->getExtName()])) {
            $extData = $data['exts'][$this->getExtName()];
        }
        
        $extModel = $this->persistModel($model, $data, $extData);
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
    
    public function onGetExtModel($e) {
        $model = $e->getParam('model');
        $extModel = $this->getModel($model);
        if($extModel) {
            $model->ext($this->getExtName(), $extModel);
        }
    }
    
    public function onGetExtsModel($e) {
        try {
            $this->onGetExtModel($e);
        } catch(ModelNotFoundException $e) {
            //matuszemi: we want to ignore these exceptions when we load extensions implicitly
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