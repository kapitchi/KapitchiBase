<?php
namespace KapitchiBase\ServiceManager;

/**
 * Event 'init' will be triggered on objects implementing this interface.
 * The code above is responsible for it. It's added to index.php currently.
 * 
 * $serviceManager->addInitializer(function ($instance) use ($serviceManager) {
 *   if($instance instanceof \KapitchiBase\ServiceManager\InitializerInitEvent) {
 *       $instance->getEventManager()->trigger('init', $instance);
 *   }
 * }, false);
 * 
 * @author Matus Zeman <mz@kapitchi.com>
 */
interface InitializerInitEvent extends \Zend\EventManager\EventManagerAwareInterface
{
    
}