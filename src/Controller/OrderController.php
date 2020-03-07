<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{

    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    public function __construct(
        ProductRepository $productRepository,
        UserRepository $userRepository,
        OrderRepository $orderRepository
    )
    {
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
    }


    /**
     * @Route("/orders", name="order")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/OrderController.php',
        ]);
    }

    /**
     * @Route("/order", name="add_order", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $userId = $data['user_id'];
        //$productsList = $data['products'];
        $address  = $data['address'];
        $shippingType = $data['shipping_type'];

        //validate user
        $user = $this->userRepository->find($userId);
        if(!$user){
            return new JsonResponse(['status' => 'Error!', 'message' => "User not found"], Response::HTTP_NOT_FOUND);
        }

        //shipping costs calculation
        $shippingCost = 10;

        $order = $this->orderRepository->saveOrder(
            $user,
            $address,
            $shippingType,
            $shippingCost
        );

        //save all products to order_products table

        return new JsonResponse(['status' => 'Order created!', 'entity' => $order->asArray()], Response::HTTP_CREATED);
    }
}
