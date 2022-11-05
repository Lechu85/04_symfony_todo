<?php

namespace App\Service;

use App\Entity\User;
//use Knp\Snappy\Pdf;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;
use Twig\Environment;

class SendingEmail
{
    private MailerInterface $mailer;
    private Environment $twig;
    //private Pdf $pdf;
    private EntrypointLookupInterface $entrypointLookup;

// Pdf $pdf,
    public function __construct(MailerInterface $mailer, Environment $twig,EntrypointLookupInterface $entrypointLookup)
    {

        $this->mailer = $mailer;
        $this->twig = $twig;
        //$this->pdf = $pdf;
        $this->entrypointLookup = $entrypointLookup;
    }

    public function sendWelcomeMessage(User $user): TemplatedEmail
    {
        $email = (new TemplatedEmail())
            ->from(new Address('test@sotech.pl', 'Sotech test'))
            ->to('work@sotech.pl')//$user->getEmail()
            ->subject('Pierwszy email z Symfony')
            ->htmlTemplate('email/welcome.html.twig')
            ->context([
                'user' => $user
            ]);

        $this->mailer->send($email);

        return $email;
    }

    public function sendAuthorWeeklyReportMessage(User $author, array $articles): TemplatedEmail
    {

        $html = $this->twig->render('email/author-weekly-report-pdf.html.twig',[
            'articles' => $articles
        ]);

        //info mówi modułowi, żeby zapomniał, że cośrenderował i za każdym wywołaniem pętli w twigu była cała tablica ze stylami.
        $this->entrypointLookup->reset();


        //info Metoda ta bierze treść html zapisuje w pliku tymczasowym
        // jeżeli wszystko pójdzie ok, to zmienna pdf będzie tekstem z zawartością poprawnym plikiem pdf.
        // Możemy z tym zrobić co chcemy. Np zapisac do pliku albo załączyc do emaila.
        //$pdf = $this->pdf->getOutputFromHtml($html);


        $email = (new TemplatedEmail())
            ->from(new Address('test@sotech.pl', 'Sotech test'))
            ->to(new Address('work@sotech.pl', $author->getFirstName()))
            //->to(new Address($author->getEmail(), $author->getFirstName()))
            ->subject('Twój tygodniowy raport')
            ->htmlTemplate('email/author-weekly-report.html.twig')
            ->context([
                'author' => $author,
                'articles' => $articles
            ]);
            //->attach($pdf, sprintf('weekly-report-%s.pdf', date('Y-m-d')));
        //info jak mamy coś dużego, to możemy zrobić fopen() i podać (file handle) do pliku

        $this->mailer->send($email);

        return $email;

    }
}