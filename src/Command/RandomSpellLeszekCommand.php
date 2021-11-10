<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:random-spell-leszek',
    description: 'Rzuć przypadkowe zaklęcie. ',
)]
class RandomSpellLeszekCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('your-name', InputArgument::OPTIONAL, 'Your name')

            ->addOption('yell', null, InputOption::VALUE_NONE, 'Krzyczysz?')
        ;
    }
    //jak ktoś wywoła komende to wykonuje sięta metoda.
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $yourName = $input->getArgument('your-name');

        if ($yourName) {
            $io->note(sprintf('Cześć %s', $yourName));
        }

        if ($input->getOption('yell')) {
            // ...
        }

        $io->success('Mamy piewrwszą komende ! Wpisz --help to see your options.');

        return Command::SUCCESS;
    }
}
