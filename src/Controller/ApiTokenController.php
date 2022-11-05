<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiTokenController extends BaseController
{
    /**
     * @Route("/api/new2", name="app_new_api_token")
     *
     * NOTE Tworzymy nowy token api
     *
     */

    public function new2(UserRepository $userRepository, EntityManagerInterface $entityManager): JsonResponse
    {

        $user = $entityManager->getRepository(User::class)->findOneBy(array('email' => 'leszek.leszek@gmail.com'));


        $user->removeApiToken();

        $apiToken = new ApiToken($user);
        $entityManager->persist($apiToken);
        $entityManager->flush();

        dd('APItoken',$apiToken->getToken());

        //return new JsonResponse(['token' => $user->getApiTokens()]);

    }
}