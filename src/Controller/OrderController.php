<?php

namespace App\Controller;

use App\Repository\OrderProductRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\AddressComposer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

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

    /**
     * @var OrderProductRepository
     */
    private $orderProductRepository;

    private $addressComposer;

    public function __construct(
        ProductRepository $productRepository,
        UserRepository $userRepository,
        OrderRepository $orderRepository,
        OrderProductRepository $orderProductRepository,
        AddressComposer $addressComposer
    )
    {
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
        $this->orderProductRepository = $orderProductRepository;
        $this->addressComposer = $addressComposer;
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
        $productsList = $data['products'];
        $address  = $data['address'];
        $shippingType = $data['shipping_type'];

        /**
         * prepare address
         */
        //$this->addressComposer->loadAddressArray($address);
        $validator = Validation::createValidator();
        $errors = $validator->validate($this->addressComposer);
        dump($errors); die();
        if (count($errors) > 0) {
            return new JsonResponse(['status' => 'Error!', $errors], Response::HTTP_BAD_REQUEST);
        }

        $address = $this->addressComposer->composeAdress();

        if(!in_array($shippingType, ['Express', 'Standard'])){
            return new JsonResponse(['status' => 'Error!', 'message' => "Incorrect shipping type"], Response::HTTP_NOT_FOUND);
        }
        //validate user
        $user = $this->userRepository->find($userId);
        if(!$user){
            return new JsonResponse(['status' => 'Error!', 'message' => "User not found"], Response::HTTP_NOT_FOUND);
        }

        $productsArray = [];
        $mugsCount = 0;
        $tshirtsCount = 0;
        foreach ($productsList as $productId){
            $product = $this->productRepository->find($productId);
            if(!$product){
                return new JsonResponse(['status' => 'Error!', 'message' => "Product not found"], Response::HTTP_NOT_FOUND);
            }
            if($product->getProductType()->getName() === 'mug'){
                $mugsCount++;
            }
            if($product->getProductType()->getName() === 't-shirt'){
                $tshirtsCount++;
            }

            $productsArray[] = $product;
        }

        //cumulative cost for products
        $totalProductsCost = 0;
        foreach ($productsArray as $product){
            $totalProductsCost += $product->getCost();
        }

        //shipping costs calculation
        $shippingCost = 0;
        $count = count($productsList);
        $adressType = $this->addressComposer->getAddressType();
        if($shippingType === 'Express'){
            if($adressType === 'International'){
                return new JsonResponse(['status' => 'Error!', 'message' => "Express delivery is only for domestic orders"], Response::HTTP_EXPECTATION_FAILED);
            }
            $shippingCost = 1000*$count;
        }

        if($shippingType === 'Standard'){
            if($adressType === 'Domestic'){
                if($mugsCount){
                    $shippingCost += 200;
                    $shippingCost += 100*($mugsCount-1);
                }
                if($tshirtsCount){
                    $shippingCost += 100;
                    $shippingCost += 50*($tshirtsCount-1);
                }
            }
            if($adressType === 'International'){
                if($mugsCount){
                    $shippingCost += 500;
                    $shippingCost += 250*($mugsCount-1);
                }
                if($tshirtsCount){
                    $shippingCost += 300;
                    $shippingCost += 150*($tshirtsCount-1);
                }
            }
        }

        //total charge calculation
        $totalCharge = $shippingCost+$totalProductsCost;

        //checking if user has enough money
        if($user->getBalance()<$totalCharge){
            return new JsonResponse(['status' => 'Error!', 'message' => "Insufficient funds"], Response::HTTP_PAYMENT_REQUIRED);
        }


        //save all necessary data
        $order = $this->orderRepository->saveOrder(
            $user,
            $address,
            $shippingType,
            $shippingCost
        );

        //save all products to order_products table
        foreach ($productsArray as $product){
            $this->orderProductRepository->saveOrderProduct($order, $product, $user);
        }

        //charge user
        $newBalance = $user->getBalance()-$totalCharge;
        $user->setBalance($newBalance);
        $this->userRepository->updateUser($user);

        return new JsonResponse(['status' => 'Order created!', 'entity' => $order->asArray()], Response::HTTP_CREATED);
    }
}
