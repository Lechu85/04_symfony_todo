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
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

//info zakomentowany przykąłd to było gdy ręcznierobiliśmy formy
//info już nie trzeba implementowac, i dziedziczyć, bo klasa AbstractLoginFormAuthenticator  robi to wszystko
//class LoginFormAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    private UserRepository $userRepository;
    private RouterInterface $router;

    //info tutaj robimy autowire - tylko w kontrolerze można w metode dodac

    public function __construct(UserRepository $userRepository, RouterInterface $router)
    {

        $this->userRepository = $userRepository;
        $this->router = $router;
    }

    //NOTE: serce naszego authenticate - zadaniem tej klasy jest oznaczenie kim jest ten user który próbuje się logować.
    // który obiekt to jest i drugie, dowód że to jest ten user w formie bedzie to hasło
    // komunikujemy to zwracajac passport obiekt

    public function authenticate(Request $request): PassportInterface
    {

        $email = $request->request->get('email');
        $password = $request->request->get('password');

        //credentials to hasło

        return new Passport(
            //NOTE tutaj tzw UserProvider dostarcza informacje o userze, w config jest provider info
            // tutaj user intentyfikator. dla nas to email
            // możemy wstawić zapytanie niestandardowe o usera.

            //NOTE UserBadge jest by dostarczyc User object
            // poniewaz daliśmy drugi parametr to my zrobiliśmy ta prace.
            // ale jeśli dostarczysz jeden parametr to UserProviderr zrobi tą robote.
            new UserBadge($email, function ($userIdentifier) {

                $user = $this->userRepository->findOneBy(['email' => $userIdentifier]);
                //NOTE tutaj możesz użyć dowolnego zapytania

                if (!$user) {
                    throw new UserNotFoundException();
                    //NOTE jak nie ma musimy wyrzucic wyjątek
                }

                return $user;
            }),

            //NOTE tutaj wersja automatyczna, sam hashuje
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

        //NOTE obiekt ten przetrzymuje badges i credential badges.
    }

    //NOTE metoda ta może np przekierować użytkownika na inny route
    // może zwrócić null, żeby kontynuować prace normalnie na komtrolerze w przypadku API autoryzacji.
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {

        if ($target = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($target);
        }

        return new RedirectResponse(
            //$this->router->generate('app_homepage')
            $this->router->generate('app_homepage')
        );

    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->router->generate('app_login');
    }


}
