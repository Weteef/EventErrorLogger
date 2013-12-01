<?php

namespace EventErrorLogger;

use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $sharedManager      = $e->getApplication()->getEventManager()->getSharedManager();

        $sharedManager->attach('Zend\Mvc\Application', \Zend\Mvc\MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onException'));
        $sharedManager->attach('Zend\Mvc\Application', \Zend\Mvc\MvcEvent::EVENT_RENDER_ERROR, array($this, 'onException'));
    }

    public function onException(MvcEvent $e)
    {
        $exception = $e->getParam('exception');

        if(isset($exception) == false){
            return;
        }

        $application    = $e->getApplication();
        $serviceManager = $application->getServiceManager();
        $config         = $serviceManager->get('config');

        if(isset($config['eventerrorlogger']) == false){
            return;
        }

        $config = $config['eventerrorlogger'];

        if($config['log'] == false){
            return;
        }

        foreach($config['loggers'] as $loggerName){
            if($serviceManager->has($loggerName) == false){
                continue;
            }

            $logger = $serviceManager->get($loggerName);

            if($logger instanceof \Zend\Log\LoggerInterface == false){
                continue;
            }

            $routeMatch = $e->getRouteMatch();

            if(isset($routeMatch)){
                $logger->crit($routeMatch->getMatchedRouteName());
                $logger->crit($routeMatch->getParams());
            }

            $logger->crit($e->getRequest());
            $logger->crit($exception);
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/eventerrorlogger.global.php.dist';
    }
}