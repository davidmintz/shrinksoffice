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
            foreach ($days_appointments  as $appointment) {
                $appointment_date = \DateTime::createFromInterface($when);
                preg_match('/ (\d{1,2})(\d\d)$/',$appointment,$m);
                $hr = (int)$m[1]; $mm = (int)$m[2];
                $appointment_date->setTime($hr,$mm,);
                //echo "appointment: ".$appointment_date->format('D Y-m-d H:i:s'),"\n";
                $patient = array_pop($patients);
                if (count($patients) === 0) {
                    echo "DEBUG: last patient\n";
                    $other_patient = PersonFactory::createOne(['active'=>true,'type'=>PersonType::PATIENT]);
                    $these_patients = [$patient,$other_patient];
                    $fee = max([$patient->getFee(),$other_patient->getFee()]);
                } else {
                    $these_patients = [$patient,];
                    $fee = $patient->getFee();
                }
                $session = ServiceFactory::createOne([
                        'patients'=>$these_patients,
                        'time'=>$appointment_date,
                        'date'=>$appointment_date,
                        'fee' => $fee,
                    ]
                );
            }
            $when->add(new \DateInterval('P1D'));
        }
        // and repeat...
        $sessions = ServiceFactory::all();
        $patients = PersonFactory::all();
        foreach ($sessions as $session) {
            $patient = array_pop($patients);
            $date = \DateTime::createFromInterface($session->getDate());
            for ($i = 0; $i <= 16; $i++) {
                $date->add(new \DateInterval('P7D'));
                ServiceFactory::createOne(
                    [
                        'patients'=>$session->getPatients(),
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
