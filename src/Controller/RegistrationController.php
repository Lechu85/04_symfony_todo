<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Model\RegistrationFormModel;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\SendingEmail;
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
    public function register(SendingEmail $mailer, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, VerifyEmailHelperInterface $verifyEmailHelper): Response
    {
        //info mamy tutaj user model, bo zrobilismy specjalną klase model bo są różne dziwne pola :)
        $form = $this->createForm(RegistrationFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var RegistrationFormModel $userModel */
            $userModel = $form->getData();

            $user = new User();
            $user->setEmail($userModel->email);
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $userModel->plainPassword
                )
            );

            if (true === $userModel->agreeTerms) {
                $user->agreeToTerms();
            }
            //$user->setSubscribeToNewsletter($userModel->subscribeToNewsletter);

            $entityManager->persist($user);
            $entityManager->flush();

            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            $mailer->sendWelcomeMessage($user);

            $this->addFlash('success', 'Potwierdź swój email : <a href="'.$signatureComponents->getSignedUrl().' target="_blank">CLICK</a>');

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
