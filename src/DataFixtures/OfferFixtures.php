<?php

namespace App\DataFixtures;

use App\Entity\Offer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;


class OfferFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $offerArray = [];
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 10; $i++) {

            $offers = new Offer();
            $offers->setName($faker->word() . ' special offer')
                ->setPrice($faker->randomFloat(2))
                ->setFeatures($faker->paragraph(3));
            $offerArray[] = $offers;
            $manager->persist($offers);
        }
    }
}
