<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $manager
    )
    {
        parent::__construct($registry, User::class);
        $this->manager = $manager;
    }

    public function saveUser($name, $balance):User
    {
        $user = new User();

        $user
            ->setName($name)
            ->setBalance($balance);

        $this->manager->persist($user);
        $this->manager->flush();

        return $user;
    }

    public function updateUser(User $user)
    {
        $this->manager->persist($user);
        $this->manager->flush();
    }
}
