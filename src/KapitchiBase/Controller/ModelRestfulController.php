<?php

namespace KapitchiBase\Controller;

use Zend\Mvc\Controller\RestfulController as ZendRestfulController,
    Zend\View\Model\JsonModel;

class ModelRestfulController extends ZendRestfulController {
    protected $modelService;
    
    public function create($data) {
        $service = $this->getModelService();
        $ret = $service->persist($data);
        
        $jsonModel = new JsonModel($ret);
        return $jsonModel;
    }
    
    public function delete($id) {
        $service = $this->getModelService();
        $ret = $service->remove($id);
        
        $jsonModel = new JsonModel($ret);
        return $jsonModel;
    }
    
    public function get($id) {
        $service = $this->getModelService();
        $model = $service->get(array(
            'priKey' => $id
        ));
        
        $ret = array(
            'id' => $id,
            'model' => $model->toArray(),
        );
        
        $jsonModel = new JsonModel($ret);
        return $jsonModel;
    }

    public function getList() {
        $service = $this->getModelService();
        
        //TODO paginator params
        $paginator = $service->getPaginator();
        $items = array();
        foreach($paginator as $item) {
            $items[] = $item->toArray();
        }
        
        $ret = array(
            'models' => $items,
            'totalModelCount' => $paginator->getTotalItemCount(),
        );
        
        $jsonModel = new JsonModel($ret);
        return $jsonModel;
    }

    public function update($id, $data) {
        $service = $this->getModelService();
        $ret = $service->persist($data);
        
        $jsonModel = new JsonModel($ret);
        return $jsonModel;
    }
    
    public function getModelService() {
        return $this->modelService;
    }

    public function setModelService($modelService) {
        $this->modelService = $modelService;
    }

}