<?php

namespace App\Command;

use App\Factory\ServiceFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Faker\Factory;


#[AsCommand(name: 'app:fake-data', aliases: ['fake'])]
class Faker extends Command
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {

        $faker = Factory::create();
        for ($i=0; $i<40; $i++) {
            $raw = $faker->phoneNumber();
            $stripped = substr(preg_replace('/\D/','',$raw), -10);
            //$stripped = preg_replace('/^1/','',$stripped);

            $output->writeln("$raw --> $stripped");
        }

        $output->writeln('<info>Fake data!</info>');
        return Command::SUCCESS;
    }
}
