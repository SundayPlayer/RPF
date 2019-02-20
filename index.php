<?php

require_once 'vendor/autoload.php';

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use React\Http\Server;
use React\MySQL\QueryResult;
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

$hello = function () {
    return new Response(200, ['Content-Type' => 'text/plain'], 'Hello');
};

$listUsers = function () use ($db) {
    return $db->query('SELECT * FROM `user` ORDER BY id')
        ->then(function (QueryResult $queryResult) {
            $users = json_encode($queryResult->resultRows);

            return new Response(200, ['Content-type' => 'application/json'], $users);
        });
};

$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $routes) use ($hello, $listUsers) {
    $routes->addRoute('GET', '/', $hello);
    $routes->addRoute('GET', '/users', $listUsers);
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
