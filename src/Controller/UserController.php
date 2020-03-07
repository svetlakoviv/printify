<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    const INITIAL_BALANCE = 100;
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/users", name="users")
     */
    public function index()
    {
        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $data[] = $user->asArray();
        }
        return $this->json($data);
    }

    /**
     * @Route("/user", name="add_user", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'];

        if (!$name ) {
            throw new NotAcceptableHttpException('Name is missing!');
        }

        $user = $this->userRepository->saveUser($name, self::INITIAL_BALANCE);

        return new JsonResponse(['status' => 'User created!', 'entity' => $user->asArray()], Response::HTTP_CREATED);
    }
}
