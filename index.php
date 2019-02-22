<?php

require_once 'vendor/autoload.php';

use App\Core\Dispatcher\Dispatcher;
use App\Core\Persistence\MySQL;
use App\Core\Service\ConfigParser;
use App\Core\Service\Container;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use React\Http\Server;
use React\Socket\Server as SocketServer;
use React\EventLoop\Factory as EventLoopFactory;

// Get configuration
$parameters = ConfigParser::parameters('config/parameters.yml');
$controllers = ConfigParser::parameters('config/controllers.yml');

$debug = true;

$container = Container::getContainer();

// Creation of needed services
$container->add('loop', EventLoopFactory::create());
$container->add('db', MySQL::initLazyConnection($parameters['database'], $container->get('loop')));
$container->add('dispatcher', Dispatcher::getDispatcher($controllers, $debug));

if ($debug) {
    // Test db connection
    $container->get('db')->ping()->then(function () {
        echo 'MySQL connection OK' . PHP_EOL;
    }, function (Exception $e) {
        echo 'MySQL connection Error: ' . $e->getMessage() . PHP_EOL;
    });
}

$server = new Server(function (ServerRequestInterface $request) {
    $dispatcher = Container::getContainer()->get('dispatcher');

    $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

    switch ($routeInfo[0]) {
        case \FastRoute\Dispatcher::NOT_FOUND:
            return new Response(404, ['Content-Type' => 'text/plain'], 'Not found');
        case \FastRoute\Dispatcher::FOUND:
            $params = $routeInfo[2] ?? [];
            return $routeInfo[1]($request, ... array_values($params));
        case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            return new Response(405, ['Content-Type' => 'text/plain'], 'Method not allowed');
        default:
            throw new LogicException;
    }
});

$socket = new SocketServer('127.0.0.1:8080', $container->get('loop'));
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;
$container->get('loop')->run();
