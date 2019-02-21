<?php

namespace App\Core\Persistence;

use React\EventLoop\LoopInterface;
use React\MySQL\ConnectionInterface;
use React\MySQL\Factory;
use React\Socket\ConnectorInterface;

class MySQL extends Factory
{
    /**
     * @param array $param
     * @param LoopInterface $loop
     * @param ConnectorInterface|null $connector
     * @return ConnectionInterface
     */
    public static function initLazyConnection(array $param, LoopInterface $loop, ?ConnectorInterface $connector = null)
    {
        $factory = new parent($loop, $connector);

        return $factory->createLazyConnection("{$param['username']}:{$param['password']}@{$param['host']}:{$param['port']}/{$param['dbname']}");
    }
}
