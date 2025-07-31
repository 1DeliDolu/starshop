<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\StarshipRepository;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:ship:check-in',
    description: 'Check-in a ship',
)]
class ShipCheckInCommand extends Command
{
    public function __construct(
        private StarshipRepository $shipRepo,
        private EntityManagerInterface $em,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('slug', InputArgument::REQUIRED, 'The slug of the starship')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $slug = $input->getArgument('slug');

        $ship = $this->shipRepo->findOneBy(['slug' => $slug]);

        if (!$ship) {
            $io->error(sprintf('Starship with slug "%s" not found.', $slug));
            return Command::FAILURE;
        }

        $this->em->remove($ship);
        $this->em->flush();

        $io->success('Starship checked-in.');

        return Command::SUCCESS;
    }
}
