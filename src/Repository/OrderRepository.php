<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $manager)
    {
        parent::__construct($registry, Order::class);
        $this->manager = $manager;
    }

    public function saveOrder($user,
                              $address,
                              $shippingType,
                              $shippingCost
    ):Order
    {
        $order = new Order();

        $order
            ->setUser($user)
            ->setAddress($address)
            ->setType($shippingType)
            ->setShipping($shippingCost)
        ;

        $this->manager->persist($order);
        $this->manager->flush();

        return $order;
    }
}
