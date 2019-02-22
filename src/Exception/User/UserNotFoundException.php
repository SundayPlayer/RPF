<?php

namespace App\Exception\User;

class UserNotFoundException extends \RuntimeException
{
    public function __construct($message = 'User not found')
    {
        parent::__construct($message);
    }
}
