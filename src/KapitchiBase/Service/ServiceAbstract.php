<?php

namespace KapitchiBase\Service;

use Zend\Loader\LocatorAware,
        Zend\Di\Locator,
        Zend\EventManager\EventCollection,
       Zend\EventManager\EventManager;


class ServiceAbstract implements LocatorAware {
    /**
     *
     * @var Zend\Loader\Locator;
     */
    protected $locator;
    
    protected function triggerParamsMergeEvent($event, array $params) {
        $eventRet = $this->triggerEvent($event, $params);
        foreach($eventRet as $event) {
            if(is_array($event) || $event instanceof Traversable) {
                $params = array_merge_recursive($params, $event);
            }
        }
        
        return $params;
    }
    
    protected function triggerEvent($event, $params) {
        return $this->events()->trigger($event, $this, $params);
    }
    
    public function setLocator(Locator $locator) {
        $this->locator = $locator;
    }
    
    public function getLocator() {
        return $this->locator;
    }
    
    //Zend/EventManager/ProvidesEvents trait
    /**
     * @var EventCollection
     */
    protected $events;

    /**
     * Set the event manager instance used by this context
     * 
     * @param  EventCollection $events 
     * @return mixed
     */
    public function setEventManager(EventCollection $events)
    {
        $this->events = $events;
        return $this;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     * 
     * @return EventCollection
     */
    public function events()
    {
        if (!$this->events instanceof EventCollection) {
            $identifiers = array(__CLASS__, get_class($this));
            if (isset($this->eventIdentifier)) {
                if ((is_string($this->eventIdentifier))
                    || (is_array($this->eventIdentifier))
                    || ($this->eventIdentifier instanceof Traversable)
                ) {
                    $identifiers = array_unique(array_merge($identifiers, (array) $this->eventIdentifier));
                } elseif (is_object($this->eventIdentifier)) {
                    $identifiers[] = $this->eventIdentifier;
                }
                // silently ignore invalid eventIdentifier types
            }
            $this->setEventManager(new EventManager($identifiers));
        }
        return $this->events;
    }
    
    //END
    
}