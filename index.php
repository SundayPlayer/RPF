<?php

require_once 'vendor/autoload.php';

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

$hello = function () {
    return new Response(200, ['Content-type' => 'text/plain'], 'Hello');
};

$server = new Server($hello);
$socket = new SocketServer('127.0.0.1:8080', $loop);
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;
$loop->run();
