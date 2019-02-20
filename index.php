<?php

require_once 'vendor/autoload.php';

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use React\Http\Server;
use React\Socket\Server as SocketServer;
use React\MySQL\Factory as MySQLFactory;
use React\EventLoop\Factory as EventLoopFactory;

$loop = EventLoopFactory::create();
$mysqlFactory = new MySQLFactory($loop);

$db = $mysqlFactory->createLazyConnection('root:root@localhost/react_php');

$db->ping()->then(function () {
    echo 'MySQL connection OK' . PHP_EOL;
}, function (Exception $e) {
    echo 'MySQL connection Error: ' . $e->getMessage() . PHP_EOL;
});

$actions = [];

// Import DefaultController
$defaultController = new \App\Controller\DefaultController();
$defaultActions = $defaultController->getActions();
$actions = array_merge($actions, $defaultActions);

// Import User Controller
$userController = new \App\Controller\UserController(new \App\Repository\UserRepository($db));
$userActions = $userController->getActions();
$actions = array_merge($actions, $userActions);

$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $routes) use ($actions) {
    foreach ($actions as $action) {
        $routes->addRoute($action['method'], $action['route'], [$action['object'], $action['action']]);
        echo "method : {$action['method']}, route : {$action['route']}, action : {$action['action']}" . PHP_EOL;
    }
});

$server = new Server(function (ServerRequestInterface $request) use ($dispatcher) {
    $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

    switch ($routeInfo[0]) {
        case Dispatcher::NOT_FOUND:
            return new Response(404, ['Content-Type' => 'text/plain'], 'Not found');
        case Dispatcher::FOUND:
            $params = $routeInfo[2] ?? [];
            return $routeInfo[1]($request, ... array_values($params));
        case Dispatcher::METHOD_NOT_ALLOWED:
            return new Response(405, ['Content-Type' => 'text/plain'], 'Method not allowed');
        default:
            throw new LogicException();
    }
});

$socket = new SocketServer('127.0.0.1:8080', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;
$loop->run();
