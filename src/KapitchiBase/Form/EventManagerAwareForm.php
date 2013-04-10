<?php
/**
 * Kapitchi Zend Framework 2 Modules (http://kapitchi.com/)
 *
 * @copyright Copyright (c) 2012-2013 Kapitchi Open Source Team (http://kapitchi.com/open-source-team)
 * @license   http://opensource.org/licenses/LGPL-3.0 LGPL 3.0
 */

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