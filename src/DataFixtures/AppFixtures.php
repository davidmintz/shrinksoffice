<?php

namespace App\DataFixtures;
use App\Factory\PersonFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager) : void
    {
        PersonFactory::createMany(30);
        $manager->flush();
    }
}
