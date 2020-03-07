<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\ProductTypeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\Routing\Annotation\Route;


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

    public function __construct(
        ProductRepository $productRepository,
        UserRepository $userRepository,
        ProductTypeRepository $productTypeRepository
    )
    {
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
        $this->productTypeRepository = $productTypeRepository;
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

        if (!$sku || !$cost || !$title || !$productTypeId || !$userId ) {
            throw new NotAcceptableHttpException('Argument is missing!');
        }

        //validate user
        $user = $this->userRepository->find($userId);
        if(!$user){
            return new JsonResponse(['status' => 'Error!', 'message' => "User not found"], Response::HTTP_NOT_FOUND);
        }
        //validate type
        $productType = $this->productTypeRepository->find($productTypeId);
        if(!$productType){
            return new JsonResponse(['status' => 'Error!', 'message' => "Product type not found"], Response::HTTP_NOT_FOUND);
        }
        //validate sku
        if($this->productRepository->findOneBy(['SKU' => $sku])){
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
