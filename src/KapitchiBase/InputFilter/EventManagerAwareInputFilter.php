<?php
namespace KapitchiBase\InputFilter;

use Zend\EventManager\EventManagerInterface,
    Zend\EventManager\EventManager;
/**
 *
 * @author Matus Zeman <mz@kapitchi.com>
 */
class EventManagerAwareInputFilter extends \Zend\InputFilter\InputFilter implements \KapitchiBase\ServiceManager\InitializerInitEvent
{
    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers(array(
            __CLASS__,
            get_called_class(),
        ));
        $this->eventManager = $eventManager;
        $this->attachDefaultListeners();
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
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