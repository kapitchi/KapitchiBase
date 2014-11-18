<?php

namespace KapitchiBase\Form;

use Traversable;
use Zend\EventManager\EventManagerAwareInterface,
    Zend\EventManager\EventManagerInterface,
    Zend\Form\Form,
    KapitchiBase\ServiceManager\InitializerInitEvent;
use Zend\Form\ElementInterface;
use Zend\Form\Fieldset;

class EventManagerAwareForm
    extends Form
    implements EventManagerAwareInterface, InitializerInitEvent
{
     /**
     * @var EventManagerInterface
     */
    protected $eventManager;
    
    /**
     * Dummy method to support tools like PoEdit. Just returns $message param
     * 
     * @param type $message
     * @param type $textDomain
     * @param type $locale
     * @return string $message param
     */
    public function translate($message, $textDomain = 'default', $locale = null)
    {
        return $message;
    }

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
     * Since I updated from 2.1.5 to 2.3.3 Form didn't wrap element names of subforms anymore.
     * This was needed here to set wrapElements to all forms added to a parent form.
     * 
     * @param array|Traversable|ElementInterface $elementOrFieldset
     * @param array $flags
     * @return Fieldset|\Zend\Form\FieldsetInterface|\Zend\Form\FormInterface
     */
    public function add($elementOrFieldset, array $flags = array())
    {
        if($elementOrFieldset instanceof Form) {
            $elementOrFieldset->setWrapElements(true);
        }
        return parent::add($elementOrFieldset, $flags);
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