<?php

namespace App\Controller;

use App\Core\Controller\Controller;
use App\Core\Http\JsonResponse;
use App\Entity\User;
use App\Exception\User\UserNotFoundException;
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
     * @return PromiseInterface
     */
    public function getAll(): PromiseInterface
    {
        $promise = $this->userRepository->getAll();

        return $promise
            ->then(function ($users) {
                return new JsonResponse(200, $users->resultRows);
            });
    }

    public function getUser(ServerRequestInterface $request, string $id): PromiseInterface
    {
        return $this->userRepository->find($id)
            ->then(function (array $user) {
                return JsonResponse::ok(['user' => $user]);
            }, function (UserNotFoundException $error) {
                return JsonResponse::notFound($error->getMessage());
            });
    }

    /**
     * @param ServerRequestInterface $request
     * @return PromiseInterface
     */
    public function createUser(ServerRequestInterface $request): PromiseInterface
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
