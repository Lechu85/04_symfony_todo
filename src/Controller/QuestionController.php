<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class QuestionController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */

    //public function homepage(Environment $twigEnvironment)
    public function homepage()
    {
        /*
        $html = $twigEnvironment->render('question/homepage.html.twig'); //użycie w taki sposó zwraca string z html
        return new Response($html);
        */

        return $this->render('question/homepage.html.twig');
        //return new Response('Pierwszy tekst w aplikacji :) ');
    }

    /**
     * @Route("/questions/{slug}", name="app_question_show")
     */
    public function show($slug)
    {
        $answers = [
            'Make sure your cat is sitting',
            'Honestly, I like furry shoes better than my cat',
            'Maybe... Try saying the spell backwards',
        ];

        dump($this);

        //render zwraca objekt Component\HttpFoundation\Response
        return $this->render('question/show.html.twig', [
            'question' => ucwords(str_replace('-',' ', $slug)),
            'answers' => $answers,
        ]);
    }
}