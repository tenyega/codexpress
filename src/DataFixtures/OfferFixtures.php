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
        for ($i = 0; $i < 10; $i++) {

            $offers = new Offer();
            $offers->setName('Premium')
                ->setPrice('4.97')
                ->setFeatures('Get the premium membership to get access to our premium code ');
            $manager->persist($offers);
        }
        $manager->flush();
    }
}
