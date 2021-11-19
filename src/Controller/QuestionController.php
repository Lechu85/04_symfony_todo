<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use App\Service\MarkdownHelper;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sentry\State\HubInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use function Sentry\init;

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
    //public function homepage(EntityManagerInterface $entityManager) // tutaj przez entity managera wczytujemy pozniej repozytorium.,
    public function homepage(QuestionRepository $repository) // tutaj przez entity managera wczytujemy pozniej repozytorium.,
    {

        //$repository = $entityManager->getRepository(Question::class);
        $questions = $repository->findAllAskedOrderByNewest();
        //$questions = $repository->findBy([], ['askedAt' => 'DESC']);


        //$html = $twigEnvironment->render('question/homepage.html.twig'); //użycie w taki sposó zwraca string z html
        //return new Response($html);

        return $this->render('question/homepage.html.twig', [
            'questions' => $questions,
        ]);
        //return new Response('Pierwszy tekst w aplikacji :) ');
    }

    /**
     * @Route("/questions/new")
     */
    public function new(EntityManagerInterface $entityManager)
    {


        return new Response('Opcja zapisywania w przygotowaniu.');

    }


    /**
     * Nowsza wersja funklcji show
     *
     * skrócona wersja dzieki sensio framework extra bundle
     * bierze nazwe zmiennej z czesci Rouite, czyli SLUG i szyka takeij w encji
     *
     * jesli podamy slug, któego nie ma w bazie, albo po id to zwraca nam 404
     * to się nazywa param converter
     *
     * czasami jak bardziej skomplikowany przypadek to nie zadziałą.
     * Dla większości przypadkó podstawowych jest ok
     *
     * @Route("/questions/{slug}", name="app_question_show")

     */
    public function show(Question $question)
    {

        if ($this->isDebug) {
            $this->logger->info('jesteśmy w trybie debug');
        }

        //w momencie kiedy zaczynamy korzystać z tablicy $answers doctrine wtedy zapytuje o to.
        //lazy load tzw.
        //$answers = $question->getAnswers();//nie dajemyu question_id ani ->ghetId()



        return $this->render('question/show.html.twig', [
            'question' => $question,
        ]);
    }


    /**
     * Poprzednia wersja funkcji z kursu, do poglądu
     * Stary czyli ta wersje jest lepsza.
     *
     * @Route("/questions/old/{slug}", name="app_question_show_old")
     */
    public function show_old($slug, MarkdownHelper $markdownHelper, EntityManagerInterface $entityManager)
    {

        if ($this->isDebug) {
            $this->logger->info('jesteśmy w trybie debug');
        }

        $repository = $entityManager->getRepository(Question::class);
        /** @var Question|null $question */
        $question = $repository->findOneBy(['slug' => $slug]);
        if (!$question) {
            throw $this->createNotFoundException(sprintf('Nie znaleziono pytania "%s"', $slug));
        }

        $answers = [
            'Make sure your cat is sitting',
            'Honestly, I like furry `shoes` better than my cat',
            'Maybe... Try saying the spell backwards',
        ];

        return $this->render('question/show.html.twig', [
            'question' => $question,
            'answers' => $answers,
        ]);
    }

    /**
     * @Route("/questions/{slug}/vote", name="app_question_vote", methods="POST")
     */
    public function questionVote(Question $question, Request $request, EntityManagerInterface $entityManager)
    {
        $direction = $request->request->get('direction');//pobiera element direction

        if ($direction === 'up') {
            $question->upVote();
        } elseif ($direction === 'down') {
            $question->downVote();
        }
        //inne ignorujemy

        $entityManager->flush();

        return $this->redirectToRoute('app_question_show', [
            'slug' => $question->getSlug(),
        ]);
    }
}