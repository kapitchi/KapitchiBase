<?php
/**
 *
 * @author Matus Zeman <mz@kapitchi.com>
 */
namespace KapitchiBase\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\EventManager\EventManagerAwareInterface,
    Zend\EventManager\EventManagerInterface;

abstract class AbstractService implements ServiceLocatorAwareInterface, EventManagerAwareInterface {
    /**
     * @var EventManagerInterface
     */
    protected $eventManager;
    
    /**
     * @var ServiceLocatorInterface
     */
    protected $locator;
    
    public function setServiceLocator(ServiceLocatorInterface $locator) {
        $this->locator = $locator;
    }
    
    public function getServiceLocator() {
        return $this->locator;
    }
    
    /**
     * Set the event manager instance used by this context
     * 
     * @param  EventManagerInterface $events
     * @return mixed
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(__CLASS__, get_called_class()));
        $this->eventManager = $events;
        $this->attachDefaultListeners();
        return $this;
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
    
    protected function triggerEvent($event, $argv = array(), $callback = null)
    {
        return $this->getEventManager()->trigger($event, $this, $argv, $callback);
    }
    
    protected function attachDefaultListeners()
    {
        
    }
    
}