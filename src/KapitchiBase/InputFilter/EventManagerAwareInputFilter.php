<?php
/**
 * Kapitchi Zend Framework 2 Modules (http://kapitchi.com/)
 *
 * @copyright Copyright (c) 2012-2013 Kapitchi Open Source Team (http://kapitchi.com/open-source-team)
 * @license   http://opensource.org/licenses/LGPL-3.0 LGPL 3.0
 */

namespace KapitchiBase\InputFilter;

use Zend\EventManager\EventManagerInterface;
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
    
    public function isValid()
    {
        $this->getEventManager()->trigger('isValid.pre', $this);
        return parent::isValid();
    }
    
    /**
     * Needed here as Zend InputFilter does not provide this and we use it plugins e.g. Contact/Company
     * @author Matus Zeman
     */
    public function getValidationGroup()
    {
        if($this->validationGroup !== null) {
            return $this->validationGroup;
        }
        
        return array_keys($this->inputs);
    }
    
    public function addValidationGroup($fields)
    {
        $fields = (array)$fields;
        $group = $this->getValidationGroup();
        $group = array_unique(array_merge($group, $fields));
        $this->validateValidationGroup($group);
        $this->setValidationGroup($group);
    }

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