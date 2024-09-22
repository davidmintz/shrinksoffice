<?php

namespace App\DataFixtures;
use App\Factory\PersonFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Person;
use App\Entity\PersonType;
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager) : void
    {
        PersonFactory::createMany(27,['active'=>true,'type'=>PersonType::PATIENT]);
        PersonFactory::createMany(2,['active'=>true,'type'=>PersonType::PAYER]);


        $patients = PersonFactory::findBy(['type'=>PersonType::PATIENT]);
        $keys = array_rand($patients,6);
        $patients[$keys[0]]->_real()->setPayer($patients[$keys[1]]->_real());
        $patients[$keys[2]]->_real()->setPayer($patients[$keys[3]]->_real());
        $payers = PersonFactory::findBy(['type'=>PersonType::PAYER]);
        $patients[$keys[4]]->_real()->setPayer($payers[0]->_real());
        $patients[$keys[5]]->_real()->setPayer($payers[1]->_real());

        $manager->flush();
    }
}
