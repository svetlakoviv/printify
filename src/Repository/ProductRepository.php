<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ProductType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $manager
    )
    {
        parent::__construct($registry, Product::class);
        $this->manager = $manager;
    }

    public function saveProduct($title,
                                $sku,
                                $cost,
                                $productType,
                                $user) :Product
    {
        $product = new Product();

        $product
            ->setTitle($title)
            ->setSKU($sku)
            ->setCost($cost)
            ->setProductType($productType)
            ->setUser($user)
        ;

        $this->manager->persist($product);
        $this->manager->flush();

        return $product;
    }
}
