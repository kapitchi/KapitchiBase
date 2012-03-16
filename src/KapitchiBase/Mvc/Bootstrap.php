<?php

namespace KapitchiBase\Mvc;

use KapitchiBase\Di\Di,
 Zend\Di\Configuration as DiConfiguration,
 Zend\Mvc\AppContext,
 Zend\Mvc\Bootstrap as ZendBootstrap;

class Bootstrap extends ZendBootstrap {
    /**
     * Sets up the locator based on the configuration provided
     * 
     * @param  AppContext $application 
     * @return void
     */
    protected function setupLocator(AppContext $application)
    {
        $di = new Di;
        $di->instanceManager()->addTypePreference('Zend\Di\Locator', $di);

        // Default configuration for common MVC classes
        $diConfig = new DiConfiguration(array('definition' => array('class' => array(
            'Zend\Mvc\Router\RouteStack' => array(
                'instantiator' => array(
                    'Zend\Mvc\Router\Http\TreeRouteStack',
                    'factory'
                ),
            ),
            'Zend\Mvc\Router\Http\TreeRouteStack' => array(
                'instantiator' => array(
                    'Zend\Mvc\Router\Http\TreeRouteStack',
                    'factory'
                ),
            ),
            'Zend\Mvc\View\DefaultRenderingStrategy' => array(
                'setLayoutTemplate' => array(
                    'layoutTemplate' => array(
                        'required' => false,
                        'type'     => false,
                    ),
                ),
            ),
            'Zend\Mvc\View\ExceptionStrategy' => array(
                'setDisplayExceptions' => array(
                    'displayExceptions' => array(
                        'required' => false,
                        'type'     => false,
                    ),
                ),
                'setExceptionTemplate' => array(
                    'exceptionTemplate' => array(
                        'required' => false,
                        'type'     => false,
                    ),
                ),
            ),
            'Zend\Mvc\View\RouteNotFoundStrategy' => array(
                'setDisplayNotFoundReason' => array(
                    'displayNotFoundReason' => array(
                        'required' => false,
                        'type'     => false,
                    ),
                ),
                'setNotFoundTemplate' => array(
                    'notFoundTemplate' => array(
                        'required' => false,
                        'type'     => false,
                    ),
                ),
            ),
            'Zend\View\HelperBroker' => array(
                'setClassLoader' => array(
                    'required' => true,
                    'loader'   => array(
                        'type'     => 'Zend\View\HelperLoader',
                        'required' => true,
                    ),
                ),
            ),
            'Zend\View\HelperLoader' => array(
                'registerPlugins' => array(
                    'map' => array(
                        'type'     => false,
                        'required' => false,
                    ),
                ),
            ),
            'Zend\View\Renderer\PhpRenderer' => array(
                'setBroker' => array(
                    'required' => true,
                    'broker'   => array(
                        'type'     => 'Zend\View\HelperBroker',
                        'required' => true,
                    ),
                ),
                'setCanRenderTrees' => array(
                    'required' => false,
                    'renderTrees' => array(
                        'type'     => false,
                        'required' => true,
                    ),
                ),
                'setResolver' => array(
                    'required' => false,
                    'resolver' => array(
                        'type'     => 'Zend\View\Resolver',
                        'required' => true,
                    ),
                ),
            ),
            'Zend\View\Resolver\AggregateResolver' => array(
                'attach' => array(
                    'resolver' => array(
                        'required' => false,
                        'type'     => 'Zend\View\Resolver',
                    ),
                ),
            ),
            'Zend\View\Resolver\TemplatePathStack' => array(
                'setDefaultSuffix' => array(
                    'defaultSuffix' => array(
                        'required' => false,
                        'type'     => false,
                    ),
                ),
                'setPaths' => array(
                    'paths' => array(
                        'required' => false,
                        'type'     => false,
                    ),
                ),
            ),
            'Zend\View\Strategy\PhpRendererStrategy' => array(
                'setContentPlaceholders' => array(
                    'contentPlaceholders' => array(
                        'required' => false,
                        'type'     => false,
                    ),
                ),
            ),
        ))));
        $diConfig->configure($di);

        $config = new DiConfiguration($this->config->di);
        $config->configure($di);

        $application->setLocator($di);
    }

    
}