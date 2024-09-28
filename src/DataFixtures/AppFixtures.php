<?php

namespace App\DataFixtures;
use App\Factory\CreditFactory;
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
use Random\RandomException;
use function Zenstruck\Foundry\get;

class AppFixtures extends Fixture
{
    private \DateTimeImmutable $start_date;

    private Array $all_sessions;
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
        printf("DEBUG: start date: %s\n",$this->start_date->format('Y-m-d'));
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
            for ($i = 0; $i <= 16; $i++) {
                $date->add(new \DateInterval('P7D'));
                // in case multiple patients
                $patients = $session->getPatients();
                if (count($patients) > 1) {
                    $fee = 30000; // keep it simple
                    $payer = $patients[0]; // array_rand($patients->toArray())] gets weird
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
        $this->loadCredits($manager);
        $manager->flush();
    }

    function loadInvoices(ObjectManager $manager)  : void
    {
        /* @todo refactor */
        $first_of_next_month = $this->start_date->add(new \DateInterval('P1M'));
        $month = $this->start_date->format('m');
        $all_sessions = ServiceFactory::all();
        $first_month_sessions = array_filter($all_sessions, fn($session) => $session->getDate()->format('m') == $month);
        // organize into array of payer_id => Service[]
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
        // and again
        $second_month_start = $this->start_date->add(new \DateInterval('P1M'));
        $second_month_sessions =  array_filter($all_sessions, fn($session) => $session->getDate()->format('m') == $second_month_start->format('m'));
        $payers = [];
        foreach ($second_month_sessions as $service) {
            $payers[$service->getPayer()->getId()][] = $service;
        }
        $invoice_date = \DateTimeImmutable::createFromInterface($second_month_start)->add(new \DateInterval('P1M'));
        foreach($payers as $payer_id => $sessions) {
            $payer = PersonFactory::find($payer_id); $payer->_disableAutoRefresh();
            InvoiceFactory::createOne([
                'payer' => $payer,
                'services' => $sessions,
                'date' =>$invoice_date,
            ]);
        }

        // one more
        $third_month_start = $this->start_date->add(new \DateInterval('P2M'));
        $third_month_sessions =  array_filter($all_sessions, fn($session) => $session->getDate()->format('m') == $third_month_start->format('m'));
        $payers = [];
        foreach ($third_month_sessions as $service) {
            $payers[$service->getPayer()->getId()][] = $service;
        }
        $invoice_date = \DateTimeImmutable::createFromInterface($third_month_start)->add(new \DateInterval('P1M'));
        foreach($payers as $payer_id => $sessions) {
            $payer = PersonFactory::find($payer_id); $payer->_disableAutoRefresh();
            InvoiceFactory::createOne([
                'payer' => $payer,
                'services' => $sessions,
                'date' =>$invoice_date,
            ]);
        }
    }

    protected function create_sessions(String $month)
    {

    }


    protected function create_payments(Array $invoices, $probability = 100) : void
    {
        foreach ($invoices as $invoice) {
            $days = range(6,32); $n_days = $days[array_rand($days)];
            $interval = new \DateInterval('P'.$n_days.'D');
            $date = \DateTime::createFromInterface($invoice->getDate())->add($interval);
            printf("DEBUG: random-ish \$date is %s\n",$date->format('Y-m-d'));
            // @todo refactor this
            $today = new \DateTime();
            if ($date->format('Ymd') >= $today->format('Ymd')) {
                $date = \DateTime::createFromFormat("U",strtotime("yesterday"));
            }

            $t = 0;
            foreach ($invoice->getServices() as $service) { $t += $service->getFee(); }
            //printf("DEBUG: current invoice total is %d\n", $t);
            if (CreditFactory::maybe($probability)) {
                CreditFactory::createOne([
                    'invoices' => [$invoice],
                    'amount' => $t,
                    'payer' => $invoice->getPayer(),
                    'date' => $date,
                ]);
            }
        }
    }

    protected function loadCredits(ObjectManager $manager) : void
    {
        // let's say we collected 100% of the first month's invoices
        $invoices = InvoiceFactory::all();
        $first_month_invoices = $this->start_date->add(new \DateInterval('P1M'))->format('m');
        $subset_invoices = array_filter($invoices, fn($invoice) => $invoice->getDate()->format('m') == $first_month_invoices);
        $this->create_payments($subset_invoices);

        $second_month_invoices = $this->start_date->add(new \DateInterval('P2M'))->format('m');
        $subset_invoices = array_filter($invoices, fn($invoice) => $invoice->getDate()->format('m') == $second_month_invoices);
        printf("DEBUG: iterating over %d invoices\n",count($subset_invoices));
        $this->create_payments($subset_invoices);

        $third_month_invoices = $this->start_date->add(new \DateInterval('P3M'))->format('m');
        $subset_invoices = array_filter($invoices, fn($invoice) => $invoice->getDate()->format('m') == $third_month_invoices);
        printf("DEBUG: iterating over %d invoices\n",count($subset_invoices));
        $this->create_payments($subset_invoices,75);

    }
}
/* SELECT i.id, i.date, TRUNCATE(SUM(s.fee)/100, 2) billed , COALESCE(TRUNCATE(c.amount/100,2),0) paid FROM invoice i
LEFT JOIN credit_invoice ci ON i.id = ci.invoice_id
LEFT JOIN credit c ON ci.credit_id = c.id JOIN service s ON s.invoice_id = i.id GROUP BY i.id; */
