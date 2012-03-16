<?php

namespace KapitchiBase\Di;

use Zend\EventManager\EventCollection,
    Zend\EventManager\EventManager;

class Di extends \Zend\Di\Di {
    /**
     * Retrieve a new instance of a class
     *
     * Forces retrieval of a discrete instance of the given class, using the
     * constructor parameters provided.
     *
     * @param mixed $name Class name or service alias
     * @param array $params Parameters to pass to the constructor
     * @param bool $isShared
     * @return object|null
     */
    public function newInstance($name, array $params = array(), $isShared = true)
    {
        $instance = parent::newInstance($name, $params, $isShared);
        
        $params = array(
            'instance' => $instance,
            'name' => $name,
            'params' => $params,
            'isShared' => $isShared,
        );
        
        $result = $this->events()->trigger('newInstance', $this, $params, function ($r) {
            return is_object($r);
        });
        if ($result->stopped()) {
            $instance = $result->last();
        }
        
        return $instance;
    }
    
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
            $identifiers = array(__CLASS__, get_class($this), 'di');
            $this->setEventManager(new EventManager($identifiers));
        }
        return $this->events;
    }
}