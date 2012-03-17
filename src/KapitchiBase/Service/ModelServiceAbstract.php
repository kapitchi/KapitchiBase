<?php

namespace KapitchiBase\Service;

use     Zend\Loader\LocatorAware,
        Zend\Di\Locator,
        Zend\EventManager\EventCollection,
        Zend\EventManager\EventManager,
        Zend\Paginator\Paginator,
        KapitchiBase\Model\ModelAbstract,
        InvalidArgumentException as NoModelFoundException;


class ModelServiceAbstract extends ServiceAbstract {
    protected $mapper;
    protected $modelPrototype;
    
    /**
     * @param array $data
     * @return type 
     */
    public function persist(array $data) {
        $mapper = $this->getMapper();
        $mapper->beginTransaction();
        
        //TODO how to ACL protect this point???
        //  resoure ................. permission.... roles
        //'KapitchiIdentity\Service', 'persist.pre', array('admin'), 
        $params = $this->triggerParamsMergeEvent('persist.pre', array('data' => $data));
        
        $model = $this->createModelFromArray($params['data']);
        $ret = $mapper->persist($model);
        $params['model'] = $model;
        
        $params = $this->triggerParamsMergeEvent('persist.post', $params);
        
        $mapper->commit();
        
        return $params;
    }
    
    public function get(array $filter, $exts = array()) {
//        if(!is_array($filter)) {
//            $filter = array('id' => $filter);
//        }
        
        $model = $this->getModelPrototype();
        $modelClass = get_class($model);
        $result = $this->events()->trigger('get.load', $this, $filter, function($ret) use ($modelClass) {
            return $ret instanceof $modelClass;
        });
        $model = $result->last();
        if(!$model instanceof $modelClass) {
            throw new NoModelFoundException("No model found filter: " . print_r($filter, true));
        }
        
        if($exts === true) {
            $this->triggerEvent('get.exts', array(
                'model' => $model,
            ));
        } else {
            foreach($exts as $ext) {
                $this->triggerEvent('get.ext.' . $ext, array(
                    'model' => $model,
                ));
            }
        }
        
        $this->triggerEvent('get.post', array(
            'model' => $model,
        ));
        
        return $model;
    }
    
    public function getPaginator($params = array()) {
        //TODO mapper should implement the interface
        $adapter = $this->getMapper()->getPaginatorAdapter($params);
        $paginator = new Paginator($adapter);
        return $paginator;
    }
    
    public function update(array $params) {
        //TODO
        throw new \Exception("N/I");
    }
    
    public function delete(array $params) {
        //TODO
        throw new \Exception("N/I");
    }
    
    protected function attachDefaultListeners() {
        parent::attachDefaultListeners();
        
        $events = $this->events();
        $mapper = $this->getMapper();
        
        //load by id
        $events->attach('get.load', function($e) use ($mapper){
            $id = $e->getParam('id');
            if(!$id) {
                return;
            }
            //TODO findById interface!!!
            return $mapper->findById($id);
        });
    }
    
    protected function createModelFromArray(array $data) {
        $model = $this->getModelPrototype();
        $model->exchangeArray($data);
        return $model;
    }
    
    public function getModelPrototype() {
        return clone $this->modelPrototype;
    }

    public function setModelPrototype(ModelAbstract $modelPrototype) {
        $this->modelPrototype = $modelPrototype;
    }

    public function setMapper($mapper) {
        $this->mapper = $mapper;
    }
    
    public function getMapper() {
        return $this->mapper;
    }
    
    
}