<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class LoginFormAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    private UserRepository $userRepository;
    private RouterInterface $router;

    //info tutaj robimy autowire - tylko w kontrolerze można w metode dodac

    public function __construct(UserRepository $userRepository, RouterInterface $router)
    {

        $this->userRepository = $userRepository;
        $this->router = $router;
    }

    //info metoda ta jest wykonywana na początku każdego Requesta - jest top firewall
    public function supports(Request $request): ?bool
    {
        // TODO: Implement supports() method.

        return $request->getPathInfo() === '/login' & $request->isMethod('POST');
        //info jezeli true, czyli jest "/login" i post wykonuje się metoda kolejna authenticate()
        //info jeżeli false request kontynuuje jak normalny kontroler i strona jest generowana
    }

    //info serce naszego authenticate - zadaniem tej klasy jest oznaczenie kim jest ten user który próbuje się logować.
    //info który obiekt to jest i drugie, dowód że to jest ten user w formie bedzie to hasło
    //info komunikujemy to zwracajac passport obiekt

    public function authenticate(Request $request): PassportInterface
    {
        // TODO: Implement authenticate() method.

        $email = $request->request->get('email');
        $password = $request->request->get('password');

        //credentials to hasło

        return new Passport(
            //info tutaj tzw UserProvider dostarcza informacje o userze, w config jest provider info
            //info tutaj user intentyfikator. dla nas to email
            //info możemy wstawić zapytanie niestandardowe o usera.

            //info UserBadge jest by dostarczyc User object
            //info poniewaz daliśmy drugi parametr to my zrobiliśmy ta prace.
            //info ale jeśli dostarczysz jeden parametr to UserProviderr zrobi tą robote.
            new UserBadge($email, function ($userIdentifier) {

                $user = $this->userRepository->findOneBy(['email' => $userIdentifier]);
                //info tutaj możesz użyć dowolnego zapytania

                if (!$user) {
                    throw new UserNotFoundException();
                    //info jak nie ma musimy wyrzucic wyjątek
                }

                return $user;
            }),

            //info tutaj wersja automatyczna, sam hashuje
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge(
                    //info to jest id użyty w formularzu, moze być dowolny.
                    'authenticate',
                    $request->request->get('_csrf_token')
                ),
                //info mechanizm ten wie jakiego checkgoba szukać o jakeij nazwie
                new RememberMeBadge(),
            ]

            //info jeśli ta metoda zwróci true wtedy leci kolejna metoda onAuthenticationSuccess
            /*new CustomCredentials(function ($credentials, User $user) {
                return $credentials === 'tada';
            }, $password)*/

        );

        //info obiekt ten przetrzymuje badges i credential badges.
    }

    //info metoda ta może np przekierować użytkownika na inny route
    //info może zwrócić null, żeby kontynuować prace normalnie na komtrolerze w przypadku API autoryzacji.
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {

        return new RedirectResponse(
            $this->router->generate('app_homepage')
        );

    }

    //info są dwa sposoby niewłąściwej autentykacji
    //info jeżeli nie ma usera o takim identyfikatorze tu jest emailu
    //info lub jeżeli nie pasuej hasło.
    //info oba kończą siętutaj.
    //info niezaleznie czy jest user czy hasło nie pasuje, zwracany jest jeden wyjątek., symfony konwertuje
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {

        //info teraz error mamy w sesji
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse(
            $this->router->generate('app_login')
        );

    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        /*
         * If you would like this class to control what happens when an anonymous user accesses a
         * protected page (e.g. redirect to /login), uncomment this method and make this class
         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
         *
         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
         */

        return new RedirectResponse(
            $this->router->generate('app_login')
        );

    }
}
