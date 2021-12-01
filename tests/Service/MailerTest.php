<?php

namespace App\Tests\Service;

use App\Entity\Article;
use App\Entity\User;
use App\Service\SendingEmail;
use Knp\Snappy\Pdf;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;
use Twig\Environment;
use Symfony\Component\Mime\Address;

class MailerTest extends KernelTestCase
{
    //Unit Test
    public function testSendWelcomeMessage(): void
    {

        $symfonyMailer = $this->createMock(MailerInterface::class);
        $symfonyMailer->expects($this->once())
            ->method('send');
        //naśladuje klase m,ailerInterface i metode ->send() wywołuje

        //info wszystkie servisy które używamy w klasie SendingEmails musimy tutaj zamockować.
        $pdf = $this->createMock(Pdf::class);
        $twig = $this->createMock(Environment::class);
        $entrypointLookup = $this->createMock(EntrypointLookupInterface::class);

        $user = new User();
        $user->setFirstName('Victor');
        $user->setEmail('victor@symfonycasts.com');

        $mailer = new SendingEmail($symfonyMailer, $twig, $pdf, $entrypointLookup);
        //info potrzebujemy jeszcze obiekt Usera.
        //info mockujemy serwisy, ale manualnie wstawiamy Encje
        $email= $mailer->sendWelcomeMessage($user);

        $this->assertSame('Welcome to the Space Bar!', $email->getSubject());
        $this->assertCount(1, $email->getTo());
        /** @var Address[] $adress */
        $adress = $email->getTo();
        $this->assertInstanceOf(Address::class, $adress[0]);
        $this->assertSame('Victor', $adress[0]->getName());
        $this->assertSame('victor@symfonycasts.com', $adress[0]->getAddress());

    }

    //Integration test
    public function testIntegrationSendAuthorWeeklyReportMessage()
    {
        self::bootKernel();
        $symfonyMailer = $this->createMock(MailerInterface::class);
        $symfonyMailer->expects($this->once())
            ->method('send');

        $pdf = self::getContainer()->get(Pdf::class);
        $twig = self::getContainer()->get(Environment::class);
        $entrypointLookup = $this->createMock(EntrypointLookupInterface::class);

        $user = new User();
        $user->setFirstName('Victor');
        $user->setEmail('victor@symfonycasts.com');
        $article = new Article();
        $article->setTitle('Black Holes: Ultimate Party Pooper');

        $mailer = new SendingEmail($symfonyMailer, $twig, $pdf, $entrypointLookup);
        $email = $mailer->sendAuthorWeeklyReportMessage($user, [$article]);
        $this->assertCount(1, $email->getAttachments());

    }

}
