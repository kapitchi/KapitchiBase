<?php

namespace KapitchiBase\Form;

use Zend\Form\Form as ZendForm,
        Zend\Form\SubForm,
        Zend\EventManager\EventCollection;

class Form extends ZendForm {
    
    public function getExtsSubForm($name = null) {
        $extsForm = $this->getSubForm('exts');
        if($extsForm === null) {
            $extsForm = new SubForm();
            $this->addSubForm($extsForm, 'exts');
        }
        
        if($name !== null) {
            return $extsForm->getSubForm($name);
        }
        
        return $extsForm;
    }
    
    public function addExtsSubForm(ZendForm $form, $name) {
        $extsForm = $this->getExtsSubForm();
        $form->setIsArray(true);
        $extsForm->addSubForm($form, $name);
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
            $identifiers = array(__CLASS__, get_class($this));
            if (isset($this->eventIdentifier)) {
                if ((is_string($this->eventIdentifier))
                    || (is_array($this->eventIdentifier))
                    || ($this->eventIdentifier instanceof Traversable)
                ) {
                    $identifiers = array_unique($identifiers + (array) $this->eventIdentifier);
                } elseif (is_object($this->eventIdentifier)) {
                    $identifiers[] = $this->eventIdentifier;
                }
                // silently ignore invalid eventIdentifier types
            }
            $this->setEventManager(new EventManager($identifiers));
        }
        return $this->events;
    }
}