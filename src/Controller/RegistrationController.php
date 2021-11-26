<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

//info dolozylem elemety z kursu symfony casts mailer - zweryfikować

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(MailerInterface $mailer, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, VerifyEmailHelperInterface $verifyEmailHelper): Response
    {

        $user = new User();
        //$userModel = $form->getData();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            //czy tu nie powinno być jeszcze setEmail() i setFirstName()
            //$user->setFirstName($userModel->firstName);
            //$user->setEmail($userModel->email);

            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            //if (true === $userModel->agreeTerms) {
            //    $user->agreeToTerms();
            //}
            //$user->setSubscribeToNewsletter($userModel->subscribeToNewsletter);

            $entityManager->persist($user);
            $entityManager->flush();

            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

/*
            $to_email = "receipient@gmail.com";
            $subject = "Simple Email Test via PHP";
            $body = "Hi,nn This is test email send by PHP Script";
            $headers = "From: sender\'s email";

            if (mail($to_email, $subject, $body, $headers)) {
                echo "Email successfully sent to $to_email...";
            } else {
                echo "Email sending failed...";
            }*/


            $email = (new TemplatedEmail())
                ->from(new Address('test@sotech.pl', 'Sotech test'))
                ->to('work@sotech.pl')//$user->getEmail()
                ->subject('Pierwszy email z Symfony')
                ->htmlTemplate('email/welcome.html.twig')
                ->context([
                    'user' => $user
                ]);


            $mailer->send($email);
            dump('MAILER:',$mailer);

            $this->addFlash('success', 'Confirm 3333 your email at: <a href="'.$signatureComponents->getSignedUrl().' target="_blank">CLICK</a>');



            // do anything else you need here, like send an email
            //return $this->redirectToRoute('app_homepage');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify', name:'app_verify_email')]
    public function verifyUserEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {

        $user = $userRepository->find($request->query->get('id'));
        if (!$user) {
            //info błąd 404
            throw $this->createNotFoundException();
        }

        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail(),
            );
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());
            return $this->redirectToRoute('app_register');
        }

        $user->setIsVerified(true);
        $entityManager->flush();

        $this->addFlash('success', 'Account Verified! You can now log in.');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/verify/resend', name:'app_verify_resend_email')]
    public function resendVerifyEmail()
    {
        return $this->render('registration/resend_verify_email.html.twig');
    }

}
