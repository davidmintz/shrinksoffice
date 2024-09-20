<?php

namespace App\DataFixtures;
use App\Factory\PersonFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Person;
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager) : void
    {
        PersonFactory::createMany(30);

        $repo = $manager->getRepository(Person::class);

        $manager->flush();
        $all = $repo->findAll();
        $ids = array_rand($all,5);
        $all[$ids[0]]->setPayer($all[$ids[1]]);
        $all[$ids[2]]->setPayer($all[$ids[3]]);
        $all[$ids[4]]->setPayer($all[$ids[3]]);
        $manager->flush();
    }
}
