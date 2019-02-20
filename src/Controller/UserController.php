<?php

namespace App\Controller;

use App\Core\Controller\Controller;
use App\Core\Http\JsonResponse;
use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function getActions(): array
    {
        return [
            ['method' => 'GET', 'route' => '/users', 'action' => 'getAll', 'object' => $this],
            ['method' => 'POST', 'route' => '/user', 'action' => 'createUser', 'object' => $this],
        ];
    }

    /**
     * UserController constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param ServerRequestInterface $request
     * @return PromiseInterface
     */
    public function getAll(ServerRequestInterface $request)
    {
        $promise = $this->userRepository->getAll();

        return $promise
            ->then(function ($users) {
                return new JsonResponse(200, $users->resultRows);
            });
    }

    public function createUser(ServerRequestInterface $request)
    {
        $body = $request->getParsedBody();

        $user = new User();
        $user
            ->setName($body['name'])
            ->setEmail($body['email']);

        return $this->userRepository->insert($user)
            ->then(
                function () { return JsonResponse::created(); },
                function (Exception $e) { return JsonResponse::badRequest($e->getMessage()); });
    }
}
