<?php

namespace App\Repository;

use App\Entity\OrderProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method OrderProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderProduct[]    findAll()
 * @method OrderProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderProductRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, OrderProduct::class);
        $this->manager = $manager;
    }

    public function saveOrderProduct($order, $product, $user):OrderProduct
    {
        $orderProduct = new OrderProduct();

        $orderProduct
            ->setPOrder($order)
            ->setProduct($product)
            ->setUser($user)
        ;

        $this->manager->persist($orderProduct);
        $this->manager->flush();

        return $orderProduct;
    }
}
