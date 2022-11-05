<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\ApiTokenRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiTokenAuthenticator extends AbstractAuthenticator
{

    private ApiTokenRepository $apiTokenRepository;

    public function __construct(ApiTokenRepository $apiTokenRepository)
    {
        $this->apiTokenRepository = $apiTokenRepository;
    }

    //NOTE Sprawdza, czy widzisz dane autoryzujące przy tym Requeście, które
    public function supports(Request $request): ?bool
    {
        //NOTE pierwsa linia abyśmy byli pewnie,że nagłowek z autoryzacją jest wysłany
        return $request->headers->has('Authorization')
            //NOTE jeżeli w wartości nagłówka autoryzacji jes tBear i spacja
            && 0 === strpos($request->headers->get('Authorization'), 'Bearer ');
    }

    //NOTE: serce naszego authenticate - zadaniem tej klasy jest oznaczenie kim jest ten user który próbuje się logować.
    // który obiekt to jest i drugie, dowód że to jest ten user w formie bedzie to hasło
    // komunikujemy to zwracajac passport obiekt
    public function authenticate(Request $request): PassportInterface
    {
        //NOTE pobieramy credentials

        $authorizationHeader = $request->headers->get('Authorization');
        $apiToken = substr($authorizationHeader, 7);
        //skip beyond "Bearer "
        //credentials to token api

        if (null === $apiToken) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        //INFO zwracamy Passport jeżeli zgadza się.
        return new Passport(
            //INFO Pierwszy parametr UserBadge identyfikuje usera
            //NOTE tutaj tzw UserProvider dostarcza informacje o userze, w config jest provider info
            // tutaj user intentyfikator. dla nas to email
            // możemy wstawić zapytanie niestandardowe o usera.

            //NOTE UserBadge jest by dostarczyc User object
            // poniewaz daliśmy drugi parametr to my zrobiliśmy ta prace.
            // ale jeśli dostarczysz jeden parametr to UserProviderr zrobi tą robote.
            //NOTE Użyj UserBadge, aby dołączyć użytkownika do paszportu
            new UserBadge($apiToken, function ($userIdentifier) {

                $user = $this->apiTokenRepository->findOneBy([
                    'token' => $userIdentifier
                ]);
                //NOTE tutaj możesz użyć dowolnego zapytania

                if (!$user) {
                    throw new UserNotFoundException();
                    //NOTE jak nie ma musimy wyrzucic wyjątek
                }
                return $user;
            }),


            //INFO jeśli ta metoda zwróci true wtedy leci kolejna metoda onAuthenticationSuccess
            new CustomCredentials(function ($credentials, User $user) {
                return $credentials === 'tada';
            }, $apiToken)

        );

        //NOTE obiekt ten przetrzymuje badges i credential badges.
    }

    //NOTE metoda ta może np przekierować użytkownika na inny route
    // Jeżeli zwróci null to, żeby kontynuować prace normalnie na komtrolerze w przypadku API autoryzacji.
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        //dump('success');
        return null;
    }

    //INFO Caution: Never use $exception->getMessage() for AuthenticationException instances. This message might contain sensitive information that you don't want to expose publicly.
    // Instead, use $exception->getMessageKey() and $exception->getMessageData() like shown in the full example above.
    //NOTE jeżeli nie zalogujemy się, można tutaj w sesji wrzucić komunikat i przekierować.
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {


        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
        //return new JsonResponse($exception->getMessage(), $exception->getCode());

    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }
}
