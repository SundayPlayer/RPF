<?php

namespace App\Controller;

use App\Core\Controller\Controller;
use App\Core\Http\JsonResponse;

class DefaultController extends Controller
{
    public function getActions(): array
    {
        return [
            ['method' => 'GET', 'route' => '/', 'action' => 'hello', 'object' => $this],
        ];
    }

    public function hello()
    {
        return new JsonResponse(200, ['Hello' => 'world !']);
    }
}