<?php

namespace App\Command;

use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use App\Service\SendingEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


#[AsCommand(
    name: 'app:author-weekly-report:send',
    description: 'Add a short description for your command',
)]
class AuthorWeeklyReportSendCommand extends Command
{
    private UserRepository $userRepository;
    private ArticleRepository $articleRepository;
    private SendingEmail $mailer;

    public function __construct(UserRepository $userRepository, ArticleRepository $articleRepository, SendingEmail $mailer)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->articleRepository = $articleRepository;

        $this->mailer = $mailer;
    }


    protected function configure(): void
    {
        $this
        ->setDescription('Wysyłka tygodniowego raportu')
            //->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            //->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //info obieklt pomagający nam drukować wyniki i informacje ładnie wyglądające.
        $io = new SymfonyStyle($input, $output);
        $authors = $this->userRepository
            ->findAllSubscribedToNewsletter();

        //info drukujemy pasek postępu
        $io->progressStart();
        foreach ($authors as $author) {
            $io->progressAdvance(count($authors)); //przekłąmuje, bo leci po każdym userze nie tylko tych subskrybujacych

            $articles = $this->articleRepository
                ->findAllPublishedLastWeekByAuthor($author);

            //Skip authors who do not have published articles for the last week
            if(count($articles) === 0) {
                continue;
            }

            $this->mailer->sendAuthorWeeklyReportMessage($author, $articles);

        }
        $io->progressFinish();

        $io->success('Tygodniowy raport został wysłany do użytkowników...');

        return Command::SUCCESS;
    }

}
