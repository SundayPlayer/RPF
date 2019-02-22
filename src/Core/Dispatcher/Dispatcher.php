<?php

namespace App\Core\Dispatcher;

use App\Core\Service\Container;
use FastRoute\RouteCollector;

class Dispatcher
{
    /**
     * Create the dispatcher
     *
     * @param array $controllersConfig
     * @param bool $debug
     * @return \FastRoute\Dispatcher
     */
    public static function getDispatcher(array $controllersConfig, bool $debug = false)
    {
        $container = Container::getContainer();

        $actions = [];

        foreach ($controllersConfig as $key => $controller) {
            foreach ($controller['actions'] as $action) {
                $action['object'] = $container->getService($controller['service']);
                $actions[] = $action;
            }
        }

        return \FastRoute\simpleDispatcher(function (RouteCollector $routes) use ($actions, $debug) {
            foreach ($actions as $action) {
                $routes->addRoute($action['method'], $action['route'], [$action['object'], $action['action']]);
                if ($debug)
                    echo "method : {$action['method']}, route : {$action['route']}, action : {$action['action']}" . PHP_EOL;
            }
        });
    }
}
