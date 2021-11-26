<?php

namespace App\Command;

use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use phpDocumentor\Reflection\Types\Context;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

#[AsCommand(
    name: 'app:author-weekly-report:send',
    description: 'Add a short description for your command',
)]
class AuthorWeeklyReportSendCommand extends Command
{
    private UserRepository $userRepository;
    private ArticleRepository $articleRepository;
    private MailerInterface $mailer;

    public function __construct(UserRepository $userRepository, ArticleRepository $articleRepository, MailerInterface $mailer)
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
            $io->progressAdvance(count($authors));

            $articles = $this->articleRepository
                ->findAllPublishedLastWeekByAuthor($author);

            //$io->warning('autor: '.$author->getFirstName());

            //Skip authors who do not have published articles for the last week
            if(count($articles) === 0) {
                continue;
            }

            $email = (new TemplatedEmail())
                ->from(new Address('test@sotech.pl', 'Sotech test'))
                ->to(new Address($author->getEmail(), $author->getFirstName()))
                ->subject('Twój tygodniowy raport')
                ->htmlTemplate('email/author-weekly-report.html.twig')
                ->context([
                    'author' => $author,
                    'articles' => $articles
                ]);

            $this->mailer->send($email);

        }
        $io->progressFinish();

        $io->success('Tygodniowy raport został wysłany do użytkowników.');

        return Command::SUCCESS;
    }

}
