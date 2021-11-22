<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use App\Service\MarkdownHelper;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * dodalismy dla pagera {pager}, musimy być ostrozni, ponieważ to jest wildcard.
     * Może zepsuć rout jezeli przypasuje pierwszy.
     * <\d+> - ma być chyba liczba - digit
     * Po dodaniu {page} do routa, trzeba przekazac parametr init $page = 1 i wywalamy obiekt Request. z parametrów funkcji
     *
     * @Route("/{page<\d+>}", name="app_homepage")
     */
    //public function homepage(EntityManagerInterface $entityManager) // tutaj przez entity managera wczytujemy pozniej repozytorium.,
    public function homepage(QuestionRepository $repository, int $page = 1) // tutaj przez entity managera wczytujemy pozniej repozytorium.,
    {

        //$repository = $entityManager->getRepository(Question::class);
        //$questions = $repository->findAllAskedOrderByNewest(); //nasza włąsna metoda zwracajaca obiekt, łączenie danych.
        //$questions = $repository->findBy([], ['askedAt' => 'DESC']); - standardowaq metoda
        $queryBuilder = $repository->createAskedOrderByNewestQueryBuilder(); //nasza włąsna metoda zwracajaca obiekt, łączenie danych.

        $pagerfanta = new Pagerfanta(
            new QueryAdapter($queryBuilder)
        );
        $pagerfanta->setMaxPerPage(5);

        $pagerfanta->setCurrentPage($page);
        //$pagerfanta->setCurrentPage($request->query->get('page', 1)); dla innego typu adresu ?page=2
        //Musi być kolejność tych dwóch powyższych m,etod


        //$html = $twigEnvironment->render('question/homepage.html.twig'); //użycie w taki sposó zwraca string z html
        //return new Response($html);
        dump($this->getUser());//jesli zalogowany to zwróci dane.





        return $this->render('question/homepage.html.twig', [
            'pager' => $pagerfanta, //obiekt pagera mozemy traktować jak tablice.
        ]);
        //return new Response('Pierwszy tekst w aplikacji :) ');
    }

    /**
     * @Route("/questions/new")
     * @IsGranted("ROLE_USER")
     */
    public function new(EntityManagerInterface $entityManager)
    {
        //info blokujemy dostep do metody
        //info jezeli rola nie przepusciła, kod niżej nie jest wykonywany
        //$this->denyAccessUnlessGranted('ROLE_USER');

        if (!$this->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('Brak dostępu dla Ciebie');
        }

    //dodać z kursu event dispatcher.strefa kursów.
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
     * @Route("/questions/edit/{slug}", name="app_question_edit")
     */
    public function edit(Question $question)
    {
        //info blokujemy dostęp - tylko zalogowany z podaną rolą
        $this->denyAccessUnlessGranted('EDIT', $question);

        return $this->render('question/edit.html.twig', [
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