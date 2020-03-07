<?php

namespace App\Repository;

use App\Entity\PrintingOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method PrintingOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrintingOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrintingOrder[]    findAll()
 * @method PrintingOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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
        parent::__construct($registry, PrintingOrder::class);
        $this->manager = $manager;
    }

    public function saveOrder($user,
                              $address,
                              $shippingType,
                              $shippingCost
    ):PrintingOrder
    {
        $order = new PrintingOrder();

        $order
            ->setUser($user)
            ->setAddress($address)
            ->setType($shippingType)
            ->setShippingCost($shippingCost)
        ;

        $this->manager->persist($order);
        $this->manager->flush();

        return $order;
    }
}
