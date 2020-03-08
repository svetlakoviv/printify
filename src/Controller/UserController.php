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
    const INITIAL_BALANCE = 10000;
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

    /**
     * @Route("/user/{userId}/products", name="get_products_for_user")
     */
    public function products($userId): JsonResponse
    {
        if (!$userId ) {
            throw new NotAcceptableHttpException('Id is missing!');
        }

        $user = $this->userRepository->find($userId);
        if(!$user){
            return new JsonResponse(['status' => 'Error!', 'message' => "User not found"], Response::HTTP_NOT_FOUND);
        }

        $products = $user->getProduct();

        foreach ($products as $product) {
            $data[] = $product->asArray();
        }
        return $this->json($data);
    }

    /**
     * @Route("/user/{userId}/orders", name="get_orders_for_user")
     */
    public function orders($userId): JsonResponse
    {
        if (!$userId ) {
            throw new NotAcceptableHttpException('Id is missing!');
        }

        $user = $this->userRepository->find($userId);
        if(!$user){
            return new JsonResponse(['status' => 'Error!', 'message' => "User not found"], Response::HTTP_NOT_FOUND);
        }

        $orders = $user->getOrder();

        foreach ($orders as $order) {
            $data[] = $order->asArray();
        }
        return $this->json($data);
    }
}
