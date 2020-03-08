<?php

namespace App\DataFixtures;

use App\Entity\ProductType;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
        $user->setName($faker->name);
        $user->setBalance($faker->randomNumber($nbDigits = 3, $strict = true));
        $manager->persist($user);
    }

        $productTypeMug = new ProductType();
        $productTypeMug->setName('mug');
        $manager->persist($productTypeMug);

        $productTypeTshirt = new ProductType();
        $productTypeTshirt->setName('t-shirt');
        $manager->persist($productTypeTshirt);

        $manager->flush();
    }
}
