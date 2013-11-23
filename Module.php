<?php

namespace EventErrorLogger;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;

class Module implements ConfigProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $sharedManager      = $e->getApplication()->getEventManager()->getSharedManager();

        $sharedManager->attach('Zend\Mvc\Application', \Zend\Mvc\MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onException'));
        $sharedManager->attach('Zend\Mvc\Application', \Zend\Mvc\MvcEvent::EVENT_RENDER_ERROR, array($this, 'onException'));
    }

    public function onException(MvcEvent $e)
    {
        $application    = $e->getApplication();
        $serviceManager = $application->getServiceManager();
        $config         = $serviceManager->get('config');

        if($config['eventerrorlogger']['log'] == false){
            return;
        }

        foreach($config['eventerrorlogger']['loggers'] as $loggerName){
            if($serviceManager->has($loggerName) == false){
                continue;
            }

            $routeMatch = $e->getRouteMatch();
            $logger = $serviceManager->get($loggerName);

            if(isset($routeMatch)){
                $logger->crit($routeMatch->getMatchedRouteName());
                $logger->crit($routeMatch->getParams());
            }

            $logger->crit($e->getRequest());
            $logger->crit($e->getParam('exception'));
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}