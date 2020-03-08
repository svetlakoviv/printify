<?php

namespace App\Controller;

use App\Repository\OrderProductRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\AddressComposer;
use App\Service\TotalOrderCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;


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

    /**
     * @var AddressComposer
     */
    private $addressComposer;

    /**
     * @var TotalOrderCalculator
     */
    private $totalOrderCalculator;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(
        ProductRepository $productRepository,
        UserRepository $userRepository,
        OrderRepository $orderRepository,
        OrderProductRepository $orderProductRepository,
        AddressComposer $addressComposer,
        TotalOrderCalculator $totalOrderCalculator,
        ValidatorInterface $validator
    )
    {
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
        $this->orderProductRepository = $orderProductRepository;
        $this->addressComposer = $addressComposer;
        $this->totalOrderCalculator = $totalOrderCalculator;
        $this->validator = $validator;
    }

    /**
     * @Route("/order", name="add_order", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $userId = $data['user_id'];
        $productsList = $data['products'];
        $address = $data['address'];
        $shippingType = $data['shipping_type'];

        /**
         * validation block
         */
        $errors[] = $this->validator->validate($userId,
            [
                new Assert\Type(['type' => 'integer']),
                new Assert\NotBlank()
            ]);
        $errors[] = $this->validator->validate($productsList,
            [
                new Assert\Type(['type' => 'array']),
                new Assert\NotBlank()
            ]);
        $errors[] = $this->validator->validate($shippingType,
            [
                new Assert\Choice(['Express', 'Standard']),
                new Assert\NotBlank()
            ]);

        $errorMessages = [];
        foreach ($errors as $error){
            if(count($error)>0){
                $errorMessages[] = (string)$error;
            }
        }
        if(count($errorMessages)>0){
            return new JsonResponse(['status' => 'Error!', 'errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        /**
         * prepare address
         */
        $this->addressComposer->loadAddressArray($address);
        $errors = $this->validator->validate($this->addressComposer);

        if (count($errors) > 0) {
            return new JsonResponse(['status' => 'Error!', 'errors' => (string)$errors], Response::HTTP_BAD_REQUEST);
        }

        $address = $this->addressComposer->composeAdress();

        /**
         * checking users, products and balance
         */
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return new JsonResponse(['status' => 'Error!', 'message' => "User not found"], Response::HTTP_NOT_FOUND);
        }

        $productsArray = [];
        foreach ($productsList as $productId) {
            $product = $this->productRepository->find($productId);
            if (!$product) {
                return new JsonResponse(['status' => 'Error!', 'message' => "Product not found"], Response::HTTP_NOT_FOUND);
            }

            $productsArray[] = $product;
        }

        $adressType = $this->addressComposer->getAddressType();
        if ($adressType === 'International') {
            return new JsonResponse(['status' => 'Error!', 'message' => "Express delivery is only for domestic orders"], Response::HTTP_EXPECTATION_FAILED);
        }

        $express = ($shippingType === 'Express');
        $totalCharge = $this->totalOrderCalculator->calculateTotalOrder($adressType, $productsArray, $express);

        //checking if user has enough money
        if ($user->getBalance() < $totalCharge) {
            return new JsonResponse(['status' => 'Error!', 'message' => "Insufficient funds"], Response::HTTP_PAYMENT_REQUIRED);
        }

        /**
         * save all necessary data
         */
        $order = $this->orderRepository->saveOrder(
            $user,
            $address,
            $shippingType,
            $totalCharge
        );

        foreach ($productsArray as $product) {
            $this->orderProductRepository->saveOrderProduct($order, $product, $user);
        }

        //charge user
        $newBalance = $user->getBalance() - $totalCharge;
        $user->setBalance($newBalance);
        $this->userRepository->updateUser($user);

        return new JsonResponse(['status' => 'Order created!', 'entity' => $order->asArray()], Response::HTTP_CREATED);
    }
}
