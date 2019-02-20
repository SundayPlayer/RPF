<?php

namespace App\Repository;

use App\Entity\User;
use React\MySQL\ConnectionInterface;
use React\Promise\PromiseInterface;

class UserRepository
{
    /**
     * @var ConnectionInterface
     */
    private $db;

    /**
     * UserController constructor.
     *
     * @param ConnectionInterface $db
     */
    public function __construct(ConnectionInterface $db)
    {
        $this->db = $db;
    }

    /**
     * @return PromiseInterface
     */
    public function getAll(): PromiseInterface
    {
        return $this->db->query('SELECT * FROM `user` ORDER BY `id`');
    }

    /**
     * @param User $user
     * @return PromiseInterface
     */
    public function insert(User $user): PromiseInterface
    {
        return $this->db->query(
            'INSERT INTO user (`name`, `email`) VALUES (?, ?)',
            [$user->getName(), $user->getEmail()]
        );
    }
}
