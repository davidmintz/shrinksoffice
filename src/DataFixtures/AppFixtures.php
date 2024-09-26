<?php

namespace App\DataFixtures;
use App\Factory\InvoiceFactory;
use App\Factory\PersonFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ObjectManager;
//use Doctrine\ORM\QueryBuilder;
//use App\Entity\Person;
use App\Factory\ServiceFactory;
use App\Entity\PersonType;
use Faker\Core\DateTime;

class AppFixtures extends Fixture
{
    private \DateTimeImmutable $start_date;
    public function load(ObjectManager $manager) : void
    {

        $times = ServiceFactory::$times;

        PersonFactory::createMany(count($times),['active'=>true,'type'=>PersonType::PATIENT]);
        $patients = PersonFactory::all();
        $when = \DateTime::createFromFormat('U',strtotime("-3 months"));
        // rewind to 1st of month
        $when->setDate($when->format('Y'),$when->format('m'),1);
        $this->start_date = \DateTimeImmutable::createFromInterface($when);

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
                ServiceFactory::createOne([
                        'patients'=>$these_patients,
                        'time'=>$appointment_date,
                        'date'=>$appointment_date,
                        'fee' => $fee,
                        'payer' => $patient->getPayer() ?? $patient,
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
                        'payer' => $patient->getPayer() ?? $patient,
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
        $this->loadSessions($manager);
        $manager->flush();
    }

    function loadSessions(ObjectManager $manager)  : void
    {
        $first_of_next_month = $this->start_date->add(new \DateInterval('P1M'));
        $sessions = ServiceFactory::all();
        $first_month_sessions = array_filter($sessions, fn($session) => $session->getDate() < $first_of_next_month);
        //printf ("DEBUG: there are %d sessions and we have %d in the first month\n", count($sessions),count($first_month_sessions));
        $payers = [];
        foreach ($first_month_sessions as $service) {

            $payers[$service->getPayer()->getId()][] = $service;
//            printf ("DEBUG: creating/finding invoice for payer %s\n", $service->getPayer()->getLastname());
//            $invoice = InvoiceFactory::findOrCreate(['payer' => $service->getPayer(),'date'=>  $first_of_next_month,]);
//            $manager->persist($invoice->_real());
//            printf ("DEBUG: adding to invoice service rendered on %s\n", $service->getDate()->format("Y-m-d"));
//            $invoice->addService($service);
        }
        foreach($payers as $payer => $sessions) {
            $payer = PersonFactory::find($payer)->_disableAutoRefresh();
            printf("DEBUG: bill  %s for services on: ",$payer->getLastname());
            foreach ($sessions as $s) { echo $s->getDate()->format('Y-m-d '); }
            echo "\n";
        }

        return;
    }
}
