<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegister()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();

        $button = $crawler->selectButton('Register');
        $form = $button->form();
        $form['user_registration_form[firstName]']->setValue('Ryan');
        $form['user_registration_form[email]']->setValue(sprintf('foo%s@example.com', rand()));
        $form['user_registration_form[plainPassword]']->setValue('space_rocks');
        $form['user_registration_form[agreeTerms]']->tick();
        $client->submit($form);

        $this->assertResponseRedirects();

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage(0);
        $this->assertEmailHeaderSame($email, 'To', 'fabien@symfony.com');

    }
}
