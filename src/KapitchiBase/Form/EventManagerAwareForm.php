<?php

namespace KapitchiBase\Form;

use Zend\EventManager\EventManagerAwareInterface,
    Zend\EventManager\EventManagerInterface,
    Zend\Form\Form,
    KapitchiBase\ServiceManager\InitializerInitEvent;

class EventManagerAwareForm
    extends Form
    implements EventManagerAwareInterface, InitializerInitEvent
{
     /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    public function setData($data)
    {
        parent::setData($data);
        
        $this->getEventManager()->trigger('setData', $this, array(
            'data' => $data
        ));
    }
    
    public function prepare()
    {
        parent::prepare();
        
        $this->getEventManager()->trigger('prepare', $this);
    }
    
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(array(
            __CLASS__,
            get_called_class(),
        ));
        $this->eventManager =  $eventManager;
        $this->attachDefaultListeners();
        return $this;
    }

    /**
     * Retrieve the event manager
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }
    
    protected function attachDefaultListeners() {
        
    }
}