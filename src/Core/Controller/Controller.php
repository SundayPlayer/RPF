<?php

namespace App\Core\Controller;

abstract class Controller
{
    public abstract function getActions(): array;
}