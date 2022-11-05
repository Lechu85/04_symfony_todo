<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskApiController extends AbstractController
{
    #[Route('/task/api', name: 'app_task_api')]
    public function index(): Response
    {
        return $this->render('task_api/index.html.twig', [
            'controller_name' => 'TaskApiController',
        ]);
    }
}
