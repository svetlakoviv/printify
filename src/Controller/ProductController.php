<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\ProductTypeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProductController extends AbstractController
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
     * @var ProductTypeRepository
     */
    private $productTypeRepository;

    private $validator;

    public function __construct(
        ProductRepository $productRepository,
        UserRepository $userRepository,
        ProductTypeRepository $productTypeRepository,
        ValidatorInterface $validator
    )
    {
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
        $this->productTypeRepository = $productTypeRepository;
        $this->validator = $validator;
    }

    /**
     * @Route("/product", name="add_product", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $sku = $data['sku'];
        $cost = $data['cost'];
        $title = $data['title'];
        $productTypeId = $data['product_type_id'];
        $userId = $data['user_id'];

        /**
         * validation block
         */

        $errors[] = $this->validator->validate($cost,
            [
                new Assert\Type(['type' => 'integer']),
                new Assert\NotBlank()
            ]);
        $errors[] = $this->validator->validate($productTypeId,
            [
                new Assert\Type(['type' => 'integer']),
                new Assert\NotBlank()
            ]);
        $errors[] = $this->validator->validate($userId,
            [
                new Assert\Type(['type' => 'integer']),
                new Assert\NotBlank()
            ]);
        $errors[] = $this->validator->validate($sku,
            [
                new Assert\NotBlank()
            ]);
        $errors[] = $this->validator->validate($title,
            [
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


        //validate user
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return new JsonResponse(['status' => 'Error!', 'message' => "User not found"], Response::HTTP_NOT_FOUND);
        }
        //validate type
        $productType = $this->productTypeRepository->find($productTypeId);
        if (!$productType) {
            return new JsonResponse(['status' => 'Error!', 'message' => "Product type not found"], Response::HTTP_NOT_FOUND);
        }
        //validate sku
        if ($this->productRepository->findOneBy(['SKU' => $sku])) {
            return new JsonResponse(['status' => 'Error!', 'message' => "This SKU is already in use"], Response::HTTP_NOT_ACCEPTABLE);
        }

        $product = $this->productRepository->saveProduct(
            $title,
            $sku,
            $cost,
            $productType,
            $user
        );

        return new JsonResponse(['status' => 'Product created!', 'entity' => $product->asArray()], Response::HTTP_CREATED);
    }
}
