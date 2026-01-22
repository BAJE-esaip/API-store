<?php

namespace App\Command;

use App\Entity\Client;
use App\Entity\Employee;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:seed-test-data',
    description: 'Seed the database with test data',
)]
class SeedTestDataCommand extends Command {
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    // protected function configure(): void {
    //     $this
    //         ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
    //         ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
    //     ;
    // }

    // protected function execute(InputInterface $input, OutputInterface $output): int {
    //     $io = new SymfonyStyle($input, $output);
    //     $arg1 = $input->getArgument('arg1');

    //     if ($arg1) {
    //         $io->note(sprintf('You passed an argument: %s', $arg1));
    //     }

    //     if ($input->getOption('option1')) {
    //         // ...
    //     }

    //     $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

    //     return Command::SUCCESS;
    // }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        $client1 = (new Client())->setEmail('john.doe@mail.local');
        $hashedPassword = $this->passwordHasher->hashPassword(
            $client1,
            'azerty1234',
        );
        $client1->setPassword($hashedPassword);
        $this->em->persist($client1);

        $employee1 = (new Employee())
            ->setEmail('alice.dubois@internal.local')
            ->setUsername('alice_dubois')
            ->setFirstName('Alice')
            ->setLastName('Dubois')
            ;
        $hashedPassword = $this->passwordHasher->hashPassword(
            $employee1,
            '1234azerty',
        );
        $employee1->setPassword($hashedPassword);
        $this->em->persist($employee1);

        $this->em->flush();
        $io->success('Database seeded');
        return Command::SUCCESS;
    }
}
