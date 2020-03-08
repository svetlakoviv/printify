<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserController extends AbstractController
{
    const INITIAL_BALANCE = 10000; //100$

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var ValidatorInterface
     */
    private $validator;


    public function __construct(
        UserRepository $userRepository,
        ValidatorInterface $validator
    )
    {
        $this->userRepository = $userRepository;
        $this->validator = $validator;
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
        $errors = $this->validator->validate($name,
            [
                new Assert\NotBlank()
            ]);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = (string)$error;
            }
            return new JsonResponse(['status' => 'Error!', 'errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->saveUser($name, self::INITIAL_BALANCE);

        return new JsonResponse(['status' => 'User created!', 'entity' => $user->asArray()], Response::HTTP_CREATED);
    }

    /**
     * @Route("/user/{userId}/products", name="get_products_for_user")
     */
    public function products(int $userId): JsonResponse
    {
        $errors = $this->validator->validate($userId,
            [
                new Assert\Type(['type' => 'integer']),
                new Assert\NotBlank()
            ]);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = (string)$error;
            }
            return new JsonResponse(['status' => 'Error!', 'errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->find($userId);
        if (!$user) {
            return new JsonResponse(['status' => 'Error!', 'message' => "User not found"], Response::HTTP_NOT_FOUND);
        }

        $products = $user->getProduct();
        $data = [];
        foreach ($products as $product) {
            $data[] = $product->asArray();
        }
        return $this->json($data);
    }

    /**
     * @Route("/user/{userId}/orders", name="get_orders_for_user")
     */
    public function orders(int $userId): JsonResponse
    {
        $errors = $this->validator->validate($userId,
            [
                new Assert\Type(['type' => 'integer']),
                new Assert\NotBlank()
            ]);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = (string)$error;
            }
            return new JsonResponse(['status' => 'Error!', 'errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->find($userId);
        if (!$user) {
            return new JsonResponse(['status' => 'Error!', 'message' => "User not found"], Response::HTTP_NOT_FOUND);
        }

        $orders = $user->getOrder();
        $data = [];
        foreach ($orders as $order) {
            $data[] = $order->asArray();
        }
        return $this->json($data);
    }
}
