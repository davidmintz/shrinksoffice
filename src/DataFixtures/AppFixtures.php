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
use function Zenstruck\Foundry\get;

class AppFixtures extends Fixture
{
    private \DateTimeImmutable $start_date;
    public function load(ObjectManager $manager) : void
    {

        $times = ServiceFactory::$times;
        PersonFactory::createMany(count($times),['active'=>true,'type'=>PersonType::PATIENT]);
        // two people will only pay, not be patients
        PersonFactory::createMany(2,['active'=>true,'type'=>PersonType::PAYER]);

        $patients = PersonFactory::findBy(['type'=>PersonType::PATIENT]);

        // randomly assign to some people a payer other than themselves
        $keys = array_rand($patients,6);
        $patients[$keys[0]]->_real()->setPayer($patients[$keys[1]]->_real());
        $patients[$keys[2]]->_real()->setPayer($patients[$keys[3]]->_real());
        $payers = PersonFactory::findBy(['type'=>PersonType::PAYER]);
        $patients[$keys[4]]->_real()->setPayer($payers[0]->_real());
        $patients[$keys[5]]->_real()->setPayer($payers[1]->_real());


        $patients = PersonFactory::findBy(['active'=>true,'type'=>PersonType::PATIENT]);
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

            // assign patients to time slots
            $days_appointments = array_filter($times, fn($time) => substr($time,0,3) == $when->format('D'));
            foreach ($days_appointments  as $appointment) {
                $appointment_date = \DateTime::createFromInterface($when);
                preg_match('/ (\d{1,2})(\d\d)$/',$appointment,$m);
                $hr = (int)$m[1]; $mm = (int)$m[2];
                $appointment_date->setTime($hr,$mm,);
                //echo "appointment: ".$appointment_date->format('D Y-m-d H:i:s'),"\n";
                $patient = array_pop($patients)->_disableAutoRefresh();
                //printf("DEBUG: patients count is now %d\n",count($patients));
                // printf("DEBUG: \$patient is a %s\n", gettype($patient));
                if (count($patients) === 0) {
                    // make one with two patients
                    $other_patient = PersonFactory::createOne(['active'=>true,'type'=>PersonType::PATIENT]);
                    $these_patients = [$patient,$other_patient];
                    $fee = 30000; // max([$patient->getFee(),$other_patient->getFee()]);
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
        foreach ($sessions as $session) {

            $date = \DateTime::createFromInterface($session->getDate());
            for ($i = 0; $i <= 4; $i++) {
                $date->add(new \DateInterval('P7D'));
                // in case multiple patients
                $patients = $session->getPatients();
                if (count($patients) > 1) {
                    $fee = 30000; // keep it simple
                    $payer = $patients[array_rand($patients->toArray())];
                } else {
                    $fee = $patients[0]->getFee();
                    $payer = $patients[0]->getPayer() ?? $patients[0];
                }

                ServiceFactory::createOne(
                    [
                        'patients'=>$patients,//$session->getPatients(),
                        'time'=>$session->getTime(),
                        'date'=>$date,
                        'fee' => $fee, //$patient->getFee(),
                        'payer' => $payer, //$patient->getPayer() ?? $patient,
                    ]
                );
            }
        }
        $this->loadInvoices($manager);
        $manager->flush();
    }

    function loadInvoices(ObjectManager $manager)  : void
    {
        $first_of_next_month = $this->start_date->add(new \DateInterval('P1M'));
        $month = $this->start_date->format('m');
        $sessions = ServiceFactory::all();
        $first_month_sessions = array_filter($sessions, fn($session) => $session->getDate()->format('m') == $month);
//        foreach ($first_month_sessions as $session) { $date = $session->getDate()->format('Y-m-d');
//            echo "session date: $date\n";
//        }
        $payers = [];
        foreach ($first_month_sessions as $service) {
            $payers[$service->getPayer()->getId()][] = $service;
        }
        foreach($payers as $payer_id => $sessions) {
            $payer = PersonFactory::find($payer_id); $payer->_disableAutoRefresh();
            InvoiceFactory::createOne([
                'payer' => $payer,
                'services' => $sessions,
                'date' => $first_of_next_month,
            ]);
        }
    }
}
