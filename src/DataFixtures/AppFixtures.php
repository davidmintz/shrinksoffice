<?php

namespace App\DataFixtures;
use App\Factory\PersonFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
//use Doctrine\ORM\QueryBuilder;
//use App\Entity\Person;
use App\Factory\ServiceFactory;
use App\Entity\PersonType;
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager) : void
    {

        $times = ServiceFactory::$times;

        PersonFactory::createMany(count($times),['active'=>true,'type'=>PersonType::PATIENT]);
        $patients = PersonFactory::all();
        $when = \DateTime::createFromFormat('U',strtotime("-3 months"));
        // rewind to 1st of month
        $when->setDate($when->format('Y'),$when->format('m'),1);

        // six days per week
        for ($i = 0; $i <= 5; $i++) {
            // advance by one if it happens to be Sunday
            if ($when->format('D') === 'Sun') {
                $when->add(new \DateInterval('P1D'));
            }
            $days_appointments = array_filter($times, fn($time) => substr($time,0,3) == $when->format('D'));
            foreach ($days_appointments  as $appt) {
                $appointment_date = \DateTime::createFromInterface($when);
                preg_match('/ (\d{1,2})(\d\d)$/',$appt,$m);
                $hr = $m[1]; $mm = (int)$m[2];
                $appointment_date->setTime($hr,$mm,);
                //echo "appointment: ".$appointment_date->format('D Y-m-d H:i:s'),"\n";
                $patient = array_pop($patients);
                $session = ServiceFactory::createOne([
                        'patients'=>[$patient],
                        'time'=>$appointment_date,
                        'date'=>$appointment_date,
                        'fee' => $patient->getFee(),
                    ]
                );
            }
            $when->add(new \DateInterval('P1D'));
        }
        // and repeat...
        $sessions = ServiceFactory::all();
        foreach ($sessions as $session) {
            $date = \DateTime::createFromInterface($session->getDate());
            for ($i = 0; $i <= 13; $i++) {
                $date->add(new \DateInterval('P7D'));
                ServiceFactory::createOne(
                    [
                        'patients'=>[$patient],
                        'time'=>$session->getTime(),
                        'date'=>$date,
                        'fee' => $patient->getFee(),
                    ]
                );
            }
        }
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
