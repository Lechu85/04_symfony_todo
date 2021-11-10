<?php

namespace App\Controller;

use App\Service\MarkdownHelper;
use Psr\Log\LoggerInterface;
use Sentry\State\HubInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class QuestionController extends AbstractController
{

    private $logger;
    private $isDebug;

    public function __construct(LoggerInterface $logger, bool $isDebug)
    {

        $this->logger = $logger;
        $this->isDebug = $isDebug;
    }
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage()
    //public function homepage(Environment $twigEnvironment)
    {

        //$html = $twigEnvironment->render('question/homepage.html.twig'); //użycie w taki sposó zwraca string z html
        //return new Response($html);

        return $this->render('question/homepage.html.twig');
        //return new Response('Pierwszy tekst w aplikacji :) ');
    }

    /**
     * @Route("/questions/{slug}", name="app_question_show")
     */
    public function show($slug, MarkdownHelper $markdownHelper, HubInterface $sentryHub)
    {
        dump($sentryHub->getClient());

        if ($this->isDebug) {
            $this->logger->info('jesteśmy w trybie debug');
        }

        throw new \Exception('Bad stuff happend'); // każdy wyjątek leci do sentry.io
        //dump($isDebug);
        //dump($this->getParameter('cache_adapter')); -pobiera parametry

        $answers = [
            'Make sure your cat is sitting',
            'Honestly, I like furry `shoes` better than my cat',
            'Maybe... Try saying the spell backwards',
        ];
        $questionText = 'I\'ve been turned into a cat, any *thoughts* on how to turn back? While I\'m **adorable**, I don\'t really care for cat food.';

        $parsedQuestionText = $markdownHelper->parse($questionText);


        //render zwraca objekt Component\HttpFoundation\Response
        return $this->render('question/show.html.twig', [
            'question' => ucwords(str_replace('-',' ', $slug)),
            'questionText' => $parsedQuestionText,
            'answers' => $answers,
        ]);
    }
}